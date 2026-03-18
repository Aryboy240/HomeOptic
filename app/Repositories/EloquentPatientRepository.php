<?php

namespace App\Repositories;

use App\Contracts\PatientRepositoryInterface;
use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPatientRepository implements PatientRepositoryInterface
{
    public function find(int $id): Patient
    {
        return Patient::with(['practice', 'doctor', 'pct'])->findOrFail($id);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Patient::query();

        // By default only active patients; include all when the checkbox is ticked
        if (empty($filters['include_all'])) {
            $query->active();
        }

        if (!empty($filters['patient_id'])) {
            $query->where('id', (int) $filters['patient_id']);
        }

        if (!empty($filters['first_name'])) {
            $query->where('first_name', 'like', '%' . $filters['first_name'] . '%');
        }

        if (!empty($filters['surname'])) {
            $query->where('surname', 'like', '%' . $filters['surname'] . '%');
        }

        if (!empty($filters['date_of_birth'])) {
            $query->whereDate('date_of_birth', $filters['date_of_birth']);
        }

        if (!empty($filters['post_code'])) {
            $query->where('post_code', 'like', '%' . $filters['post_code'] . '%');
        }

        if (!empty($filters['sex_gender'])) {
            $query->where('sex_gender', $filters['sex_gender']);
        }

        if (!empty($filters['patient_type'])) {
            $query->where('patient_type', $filters['patient_type']);
        }

        if (!empty($filters['has_glaucoma'])) {
            $query->where('has_glaucoma', true);
        }

        if (!empty($filters['is_diabetic'])) {
            $query->where('is_diabetic', true);
        }

        [$column, $direction] = $this->resolveSortOrder($filters['sort'] ?? 'surname_asc');

        return $query
            ->orderBy($column, $direction)
            ->paginate(25)
            ->withQueryString();
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient->fresh();
    }

    // -------------------------------------------------------------------------

    /**
     * Map the sort dropdown value from the Find Patient screen to a column/direction pair.
     *
     * @return array{0: string, 1: string}
     */
    private function resolveSortOrder(string $sort): array
    {
        return match ($sort) {
            'id_asc'         => ['id', 'asc'],
            'id_desc'        => ['id', 'desc'],
            'surname_desc'   => ['surname', 'desc'],
            'forename_asc'   => ['first_name', 'asc'],
            'forename_desc'  => ['first_name', 'desc'],
            'dob_asc'        => ['date_of_birth', 'asc'],
            'dob_desc'       => ['date_of_birth', 'desc'],
            'postcode_asc'   => ['post_code', 'asc'],
            'postcode_desc'  => ['post_code', 'desc'],
            default          => ['surname', 'asc'],  // surname_asc and any unrecognised value
        };
    }
}
