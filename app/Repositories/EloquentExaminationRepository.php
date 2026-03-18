<?php

namespace App\Repositories;

use App\Contracts\ExaminationRepositoryInterface;
use App\Models\Examination;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class EloquentExaminationRepository implements ExaminationRepositoryInterface
{
    public function find(int $id): Examination
    {
        return Examination::with([
            'historySymptoms',
            'ophthalmoscopy',
            'investigative',
            'refraction',
            'staff',
            'signedBy',
        ])->findOrFail($id);
    }

    public function forPatient(Patient $patient): Collection
    {
        return Examination::with('staff')
            ->where('patient_id', $patient->id)
            ->orderBy('examined_at', 'desc')
            ->get();
    }
}
