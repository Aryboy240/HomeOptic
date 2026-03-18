<?php

namespace App\Contracts;

use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    /**
     * Find a single patient by ID. Throws ModelNotFoundException if not found.
     */
    public function find(int $id): Patient;

    /**
     * Search patients with filters from the Find Patient screen.
     *
     * Recognised filter keys:
     *   patient_id   int     — exact match on primary key
     *   first_name   string  — partial match
     *   surname      string  — partial match
     *   date_of_birth string — exact match (Y-m-d)
     *   post_code    string  — partial match
     *   sex_gender   string  — SexGender enum value
     *   patient_type string  — PatientType enum value (single char A–F)
     *   has_glaucoma bool    — filter to glaucoma patients
     *   is_diabetic  bool    — filter to diabetic patients
     *   include_all  bool    — include deceased and deleted (default: active only)
     *   sort         string  — one of: id_asc, id_desc, surname_asc, surname_desc,
     *                          forename_asc, forename_desc, dob_asc, dob_desc,
     *                          postcode_asc, postcode_desc
     */
    public function search(array $filters): LengthAwarePaginator;

    /**
     * Create a new patient record.
     */
    public function create(array $data): Patient;

    /**
     * Update an existing patient record. Returns the refreshed model.
     */
    public function update(Patient $patient, array $data): Patient;
}
