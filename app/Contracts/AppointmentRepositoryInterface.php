<?php

namespace App\Contracts;

use App\Models\Appointment;
use App\Models\Diary;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentRepositoryInterface
{
    /**
     * Find a single appointment by ID with patient and diary eager-loaded.
     * Throws ModelNotFoundException if not found.
     */
    public function find(int $id): Appointment;

    /**
     * Return all appointments for a diary within an inclusive date range,
     * ordered chronologically (date then start_time).
     *
     * Used by the week and day views of the diary calendar.
     * Cancelled appointments are excluded by default; pass true to include them.
     */
    public function forDiaryAndDateRange(
        Diary $diary,
        Carbon $from,
        Carbon $to,
        bool $includeCancelled = false,
    ): Collection;

    /**
     * Create a new appointment.
     */
    public function create(array $data): Appointment;

    /**
     * Update an existing appointment. Returns the refreshed model.
     */
    public function update(Appointment $appointment, array $data): Appointment;
}
