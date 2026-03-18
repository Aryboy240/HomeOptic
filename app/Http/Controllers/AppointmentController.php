<?php

namespace App\Http\Controllers;

use App\Contracts\AppointmentRepositoryInterface;
use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\Diary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentRepositoryInterface $appointments)
    {
    }

    /**
     * Edit Appointment form.
     */
    public function edit(Appointment $appointment): View
    {
        $appointment->load(['patient', 'diary']);

        return view('appointments.edit', [
            'appointment'         => $appointment,
            'diaries'             => Diary::orderBy('name')->pluck('name', 'id'),
            'appointmentTypes'    => AppointmentType::options(),
            'appointmentStatuses' => AppointmentStatus::options(),
        ]);
    }

    /**
     * Create a new appointment from the diary.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'diary_id'           => ['required', 'exists:diaries,id'],
            'patient_id'         => ['required', 'exists:patients,id'],
            'appointment_type'   => ['required', 'string'],
            'appointment_status' => ['required', 'string'],
            'date'               => ['required', 'date'],
            'start_time'         => ['required', 'date_format:H:i,H:i:s'],
            'length_minutes'     => ['required', 'integer', 'min:1'],
            'display_text'       => ['nullable', 'string'],
        ]);

        $this->failIfConflict($validated['diary_id'], $validated['date'], $validated['start_time'], $validated['length_minutes']);

        $this->appointments->create($validated);

        return redirect()->route('diary.index', ['date' => $validated['date']])
            ->with('success', 'Appointment booked.');
    }

    /**
     * Update an existing appointment.
     * Passing cancel=1 stamps cancelled_at; omitting it leaves cancellation unchanged.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'diary_id'           => ['required', 'exists:diaries,id'],
            'appointment_type'   => ['required', 'string'],
            'appointment_status' => ['required', 'string'],
            'date'               => ['required', 'date'],
            'start_time'         => ['required', 'date_format:H:i,H:i:s'],
            'length_minutes'     => ['required', 'integer', 'min:1'],
            'display_text'       => ['nullable', 'string'],
            'cancel'             => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['cancel'])) {
            $validated['cancelled_at'] = now();
        }

        unset($validated['cancel']);

        // Only check for conflicts when the appointment has not just been cancelled.
        if (empty($validated['cancelled_at'])) {
            $this->failIfConflict(
                $validated['diary_id'], $validated['date'],
                $validated['start_time'], $validated['length_minutes'],
                $appointment->id,
            );
        }

        $this->appointments->update($appointment, $validated);

        return redirect()->route('diary.index', ['date' => $validated['date']])
            ->with('success', 'Appointment updated.');
    }

    // -------------------------------------------------------------------------

    /**
     * Throw a ValidationException if any non-cancelled appointment in the same
     * diary on the same date overlaps the requested [start_time, start_time + length_minutes) slot.
     * Pass $excludeId to ignore the appointment being updated.
     */
    private function failIfConflict(
        int|string $diaryId,
        string $date,
        string $startTime,
        int $lengthMinutes,
        ?int $excludeId = null,
    ): void {
        $reqStart = $this->timeToMinutes($startTime);
        $reqEnd   = $reqStart + $lengthMinutes;

        $existing = Appointment::where('diary_id', $diaryId)
            ->whereDate('date', $date)
            ->whereNull('cancelled_at')
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get(['id', 'start_time', 'length_minutes']);

        foreach ($existing as $appt) {
            $apptStart = $this->timeToMinutes($appt->start_time);
            $apptEnd   = $apptStart + $appt->length_minutes;

            if ($reqStart < $apptEnd && $reqEnd > $apptStart) {
                throw ValidationException::withMessages([
                    'start_time' => 'This time slot is already booked.',
                ]);
            }
        }
    }

    /**
     * Convert a H:i or H:i:s time string to minutes since midnight.
     */
    private function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);

        return (int) $h * 60 + (int) $m;
    }
}
