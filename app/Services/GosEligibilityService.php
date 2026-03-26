<?php

namespace App\Services;

use App\Enums\PatientType;
use App\Models\Patient;

class GosEligibilityService
{
    private const ELIGIBLE_BENEFITS = [
        'income_support',
        'jobseekers_allowance',
        'esa',
        'pension_credit',
        'universal_credit',
        'nhs_tax_credit_exemption',
        'hc2_certificate',
        'hc3_certificate',
    ];

    /**
     * GOS1 — routine NHS sight test.
     *
     * patient_type is NOT used as a primary eligibility criterion.
     * The only type-based criterion is FamilyHistory of glaucoma, which requires age >= 40.
     */
    public function isEligibleGos1(Patient $patient): bool
    {
        $age = $patient->date_of_birth->age;

        if ($age < 16 || $age >= 60) {
            return true;
        }

        if ($patient->in_full_time_education && $age < 19) {
            return true;
        }

        if ($patient->has_glaucoma || $patient->is_diabetic) {
            return true;
        }

        if ($patient->is_blind_partially_sighted || $patient->has_hearing_impairment || $patient->has_retinitis_pigmentosa) {
            return true;
        }

        if ($patient->patient_type === PatientType::FamilyHistory && $age >= 40) {
            return true;
        }

        if ($patient->patient_type === PatientType::Private) {
            return false;
        }

        return $this->hasEligibleBenefit($patient);
    }

    /**
     * GOS3 — NHS optical voucher.
     *
     * Age >= 60 is NOT a standalone criterion for GOS3 — it qualifies a patient for
     * a free sight test (GOS1) only. A voucher requires a specific medical or
     * financial criterion. Sensory disabilities (blind/hearing/RP) are GOS1-only.
     */
    public function isEligibleGos3(Patient $patient): bool
    {
        $age = $patient->date_of_birth->age;

        if ($age < 16) {
            return true;
        }

        if ($patient->in_full_time_education && $age < 19) {
            return true;
        }

        if ($patient->has_glaucoma || $patient->is_diabetic) {
            return true;
        }

        if ($patient->patient_type === PatientType::FamilyHistory && $age >= 40) {
            return true;
        }

        if ($patient->patient_type === PatientType::Private) {
            return false;
        }

        return $this->hasEligibleBenefit($patient);
    }

    /**
     * GOS6 — domiciliary visit claim.
     * Eligible if the patient qualifies for GOS1 and has a domiciliary reason.
     */
    public function isEligibleGos6(Patient $patient): bool
    {

        if ($patient->patient_type === PatientType::Private) {
            return false;
        }
        
        return $this->isEligibleGos1($patient) && $patient->domiciliary_reason !== null;
    }

    private function hasEligibleBenefit(Patient $patient): bool
    {
        $benefits = $patient->benefits ?? [];
        return count(array_intersect($benefits, self::ELIGIBLE_BENEFITS)) > 0;
    }
}
