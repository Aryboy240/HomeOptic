<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentType;
use App\Enums\PatientStatus;
use App\Enums\PatientTitle;
use App\Enums\PatientType;
use App\Enums\SexGender;
use App\Jobs\SendBookingRequestNotificationJob;
use App\Models\AdminNotification;
use App\Models\Appointment;
use App\Models\PendingBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicController extends Controller
{
    // Wolverhampton WV1 1AA coordinates
    private const WOLVERHAMPTON_LAT = 52.5838;
    private const WOLVERHAMPTON_LNG = -2.1280;
    private const MAX_DISTANCE_MILES = 20;

    public function home(): View
    {
        return view('public.home');
    }

    public function book(): View
    {
        return view('public.book', [
            'types'   => AppointmentType::options(),
            'titles'  => PatientTitle::options(),
            'genders' => SexGender::options(),
            'patientTypes' => PatientType::options(),
        ]);
    }

    /**
     * Return taken time slots (HH:MM) for a given date.
     * Includes confirmed appointments and non-declined pending bookings.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json([]);
        }

        // Collect all existing blocks: [start_minutes, end_minutes]
        $blocks = [];

        Appointment::whereDate('date', $date)
            ->whereNull('cancelled_at')
            ->get(['start_time', 'length_minutes'])
            ->each(function ($a) use (&$blocks) {
                $start = $this->timeToMinutes(substr($a->start_time, 0, 5));
                $blocks[] = [$start, $start + ($a->length_minutes ?? 30)];
            });

        PendingBooking::where('appointment_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->get(['appointment_time'])
            ->each(function ($b) use (&$blocks) {
                $start = $this->timeToMinutes(substr($b->appointment_time, 0, 5));
                $blocks[] = [$start, $start + 30]; // pending bookings assumed 30 min
            });

        // A 30-minute slot starting at T is taken if any block overlaps [T, T+30)
        $takenSlots = [];
        for ($h = 8; $h < 20; $h++) {
            foreach ([0, 30] as $m) {
                $slotStart = $h * 60 + $m;
                $slotEnd   = $slotStart + 30;
                foreach ($blocks as [$bStart, $bEnd]) {
                    if ($bStart < $slotEnd && $bEnd > $slotStart) {
                        $takenSlots[] = sprintf('%02d:%02d', $h, $m);
                        break;
                    }
                }
            }
        }

        return response()->json($takenSlots);
    }

    /**
     * Check whether a postcode is within range of Wolverhampton.
     */
    public function checkPostcode(Request $request): JsonResponse
    {
        $raw = $request->input('postcode', '');
        $postcode = preg_replace('/\s+/', '', $raw);

        if (empty($postcode)) {
            return response()->json(['within_range' => true, 'distance_miles' => null]);
        }

        try {
            $response = Http::timeout(5)->get("https://api.postcodes.io/postcodes/{$postcode}");

            if (!$response->successful() || !$response->json('result')) {
                return response()->json(['within_range' => true, 'distance_miles' => null]);
            }

            $result = $response->json('result');
            $distance = $this->haversineDistance(
                self::WOLVERHAMPTON_LAT, self::WOLVERHAMPTON_LNG,
                $result['latitude'], $result['longitude']
            );

            return response()->json([
                'within_range'   => $distance <= self::MAX_DISTANCE_MILES,
                'distance_miles' => round($distance, 1),
            ]);
        } catch (\Exception) {
            return response()->json(['within_range' => true, 'distance_miles' => null]);
        }
    }

    /**
     * Validate and store a new public booking request.
     */
    public function submitBooking(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Booking
            'appointment_date' => ['required', 'date', 'after:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'appointment_type' => ['required', 'string', 'in:routine_eye_test,domiciliary,follow_up'],
            'reason'           => ['required', 'string', 'max:2000'],
            'examiner_notes'   => ['nullable', 'string', 'max:2000'],
            'customer_email'   => ['required', 'email', 'max:255'],
            // Patient — core
            'title'            => ['required', 'string', 'max:20'],
            'first_name'       => ['required', 'string', 'max:255'],
            'surname'          => ['required', 'string', 'max:255'],
            'date_of_birth'    => ['required', 'date'],
            'sex_gender'       => ['required', 'string'],
            'patient_type'     => ['required', 'string'],
            // Address
            'address_line_1'   => ['required', 'string', 'max:255'],
            'town_city'        => ['required', 'string', 'max:255'],
            'county'           => ['nullable', 'string', 'max:255'],
            'post_code'        => ['required', 'string', 'max:10'],
            // Contact
            'telephone_mobile' => ['required', 'string', 'max:20'],
            'telephone_other'  => ['nullable', 'string', 'max:20'],
            'email'            => ['required', 'email', 'max:255'],
            // Medical
            'is_nhs'                     => ['nullable', 'boolean'],
            'has_glaucoma'               => ['nullable', 'boolean'],
            'is_diabetic'                => ['nullable', 'boolean'],
            'is_blind_partially_sighted' => ['nullable', 'boolean'],
            'has_hearing_impairment'     => ['nullable', 'boolean'],
            'has_retinitis_pigmentosa'   => ['nullable', 'boolean'],
            'physical_disabilities'      => ['nullable', 'string', 'max:2000'],
            'mental_health_conditions'   => ['nullable', 'string', 'max:2000'],
            // Social
            'in_full_time_education'   => ['nullable', 'boolean'],
            'benefits'                 => ['nullable', 'array'],
            'benefits.*'               => ['string'],
            'next_of_kin_name'         => ['nullable', 'string', 'max:255'],
            'next_of_kin_relationship' => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone'        => ['nullable', 'string', 'max:20'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone'  => ['nullable', 'string', 'max:20'],
            'carer_name'               => ['nullable', 'string', 'max:255'],
            'carer_phone'              => ['nullable', 'string', 'max:20'],
        ]);

        // Distance check
        $this->enforceDistanceLimit($validated['post_code']);

        // Separate booking vs patient fields
        $bookingFields = [
            'appointment_date', 'appointment_time', 'appointment_type',
            'reason', 'examiner_notes', 'customer_email',
        ];

        $patientFormData = array_diff_key($validated, array_flip($bookingFields));

        // Normalise booleans so they store as true/false in JSON
        foreach (['is_nhs','has_glaucoma','is_diabetic','is_blind_partially_sighted','has_hearing_impairment','has_retinitis_pigmentosa','in_full_time_education'] as $flag) {
            $patientFormData[$flag] = (bool) ($patientFormData[$flag] ?? false);
        }

        $booking = PendingBooking::create([
            'token'             => (string) Str::uuid(),
            'appointment_date'  => $validated['appointment_date'],
            'appointment_time'  => $validated['appointment_time'],
            'appointment_type'  => $validated['appointment_type'],
            'reason'            => $validated['reason'] ?? null,
            'examiner_notes'    => $validated['examiner_notes'] ?? null,
            'status'            => 'pending',
            'customer_email'    => $validated['customer_email'],
            'patient_form_data' => $patientFormData,
        ]);

        AdminNotification::create([
            'type'               => 'booking_request',
            'title'              => 'New Booking Request',
            'body'               => ($patientFormData['first_name'] ?? '') . ' ' . ($patientFormData['surname'] ?? '')
                                     . ' — ' . $booking->appointment_date->format('j M Y')
                                     . ' at ' . substr($validated['appointment_time'], 0, 5),
            'pending_booking_id' => $booking->id,
        ]);

        SendBookingRequestNotificationJob::dispatch($booking);

        return redirect()->route('booking.confirmed', $booking->token);
    }

    /**
     * Show the booking confirmation page.
     */
    public function bookingConfirmed(string $token): View
    {
        $pendingBooking = PendingBooking::where('token', $token)->firstOrFail();

        return view('public.confirmed', compact('pendingBooking'));
    }

    // -------------------------------------------------------------------------

    private function enforceDistanceLimit(string $postcode): void
    {
        $clean = preg_replace('/\s+/', '', $postcode);

        try {
            $response = Http::timeout(5)->get("https://api.postcodes.io/postcodes/{$clean}");

            if ($response->successful() && $response->json('result')) {
                $result   = $response->json('result');
                $distance = $this->haversineDistance(
                    self::WOLVERHAMPTON_LAT, self::WOLVERHAMPTON_LNG,
                    $result['latitude'], $result['longitude']
                );

                if ($distance > self::MAX_DISTANCE_MILES) {
                    throw ValidationException::withMessages([
                        'post_code' => ['We are unable to visit addresses more than 20 miles from Wolverhampton. Please call us to discuss.'],
                    ]);
                }
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception) {
            // Postcodes API unreachable — proceed
        }
    }

    private function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return (int) $h * 60 + (int) $m;
    }

    /**
     * Calculate straight-line distance between two lat/lng points in miles.
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthMiles = 3958.8;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthMiles * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
