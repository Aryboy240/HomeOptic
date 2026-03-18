<?php

namespace App\Http\Controllers;

use App\Contracts\AppointmentRepositoryInterface;
use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\Diary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $this->appointments->update($appointment, $validated);

        return redirect()->route('diary.index', ['date' => $validated['date']])
            ->with('success', 'Appointment updated.');
    }
}
