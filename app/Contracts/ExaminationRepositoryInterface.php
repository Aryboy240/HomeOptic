<?php

namespace App\Contracts;

use App\Models\Examination;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

interface ExaminationRepositoryInterface
{
    /**
     * Find a single examination by ID with all four tab child records eager-loaded.
     * Throws ModelNotFoundException if not found.
     */
    public function find(int $id): Examination;

    /**
     * Return all examinations for a patient, ordered newest first.
     * Used to populate the patient examination history table.
     * Eager-loads the examining staff member.
     */
    public function forPatient(Patient $patient): Collection;
}
