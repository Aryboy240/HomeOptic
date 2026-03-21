<?php

namespace App\Http\Controllers;

use App\Contracts\PatientRepositoryInterface;
use App\Enums\AppointmentStatus;
use App\Enums\PatientStatus;
use App\Jobs\SendBookingDecisionJob;
use App\Models\AdminNotification;
use App\Models\Appointment;
use App\Models\Diary;
use App\Models\PendingBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function __construct(private readonly PatientRepositoryInterface $patients)
    {
    }

    public function index(Request $request): View
    {
        // Mark all unread notifications as read when the page is viewed
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        $status   = $request->input('status', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');
        $sort     = $request->input('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = AdminNotification::with('pendingBooking')->orderBy('created_at', $sort);

        if (in_array($status, ['pending', 'approved', 'declined'], true)) {
            $query->whereHas('pendingBooking', fn ($q) => $q->where('status', $status));
        }

        if ($dateFrom) {
            $query->whereHas('pendingBooking', fn ($q) => $q->whereDate('appointment_date', '>=', $dateFrom));
        }

        if ($dateTo) {
            $query->whereHas('pendingBooking', fn ($q) => $q->whereDate('appointment_date', '<=', $dateTo));
        }

        $notifications = $query->get();

        return view('admin.notifications.index', compact('notifications', 'status', 'dateFrom', 'dateTo', 'sort'));
    }

    public function markRead(AdminNotification $notification): JsonResponse
    {
        $notification->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function approve(Request $request, PendingBooking $pendingBooking): JsonResponse
    {
        if ($pendingBooking->status !== 'pending') {
            return response()->json(['error' => 'This booking has already been decided.'], 422);
        }

        DB::transaction(function () use ($request, $pendingBooking) {
            // Create patient from submitted form data
            $patientData = $pendingBooking->patient_form_data;
            $patientData['status'] = PatientStatus::Active->value;

            $patient = $this->patients->create($patientData);

            // Create appointment — suppress AppointmentObserver to avoid double-emailing
            $diary = Diary::first();

            $appointment = Appointment::withoutEvents(function () use ($pendingBooking, $patient, $diary) {
                return Appointment::create([
                    'diary_id'           => $diary?->id ?? 1,
                    'patient_id'         => $patient->id,
                    'appointment_type'   => $pendingBooking->appointment_type,
                    'appointment_status' => AppointmentStatus::Booked->value,
                    'date'               => $pendingBooking->appointment_date,
                    'start_time'         => $pendingBooking->appointment_time,
                    'length_minutes'     => 60,
                    'display_text'       => $patient->first_name . ' ' . $patient->surname,
                ]);
            });

            $pendingBooking->update([
                'status'             => 'approved',
                'patient_id'         => $patient->id,
                'appointment_id'     => $appointment->id,
                'admin_decision_at'  => now(),
                'admin_decided_by'   => $request->user()->id,
            ]);

            SendBookingDecisionJob::dispatch($pendingBooking->fresh(), true);
        });

        return response()->json(['ok' => true, 'status' => 'approved']);
    }

    public function decline(Request $request, PendingBooking $pendingBooking): JsonResponse
    {
        if ($pendingBooking->status !== 'pending') {
            return response()->json(['error' => 'This booking has already been decided.'], 422);
        }

        $pendingBooking->update([
            'status'            => 'declined',
            'admin_decision_at' => now(),
            'admin_decided_by'  => $request->user()->id,
        ]);

        SendBookingDecisionJob::dispatch($pendingBooking, false);

        return response()->json(['ok' => true, 'status' => 'declined']);
    }
}
