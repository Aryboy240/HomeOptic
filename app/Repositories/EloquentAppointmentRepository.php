<?php

namespace App\Repositories;

use App\Contracts\AppointmentRepositoryInterface;
use App\Models\Appointment;
use App\Models\Diary;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EloquentAppointmentRepository implements AppointmentRepositoryInterface
{
    public function find(int $id): Appointment
    {
        return Appointment::with(['patient', 'diary'])->findOrFail($id);
    }

    public function forDiaryAndDateRange(
        Diary $diary,
        Carbon $from,
        Carbon $to,
        bool $includeCancelled = false,
    ): Collection {
        $query = Appointment::with('patient')
            ->where('diary_id', $diary->id)
            ->forDateRange($from, $to)
            ->orderBy('date')
            ->orderBy('start_time');

        if (!$includeCancelled) {
            $query->notCancelled();
        }

        return $query->get();
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);

        return $appointment->fresh();
    }
}
