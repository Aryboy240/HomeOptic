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
        'hc2_certificate',
        'nhs_tax_credit_exemption',
    ];

    /**
     * GOS1 — routine NHS sight test.
     */
    public function isEligibleGos1(Patient $patient): bool
    {
        $age = $patient->date_of_birth->age;

        if ($age < 16 || $age >= 60) {
            return true;
        }

        if ($patient->has_glaucoma || $patient->is_diabetic) {
            return true;
        }

        if ($patient->is_blind_partially_sighted || $patient->has_hearing_impairment || $patient->has_retinitis_pigmentosa) {
            return true;
        }

        if ($patient->in_full_time_education && $age < 19) {
            return true;
        }

        if ($this->hasEligibleBenefit($patient)) {
            return true;
        }

        $eligibleTypes = [
            PatientType::Over60,
            PatientType::NHS,
            PatientType::Child,
            PatientType::Glaucoma,
            PatientType::FamilyHistory,
        ];

        return in_array($patient->patient_type, $eligibleTypes, strict: true);
    }

    /**
     * GOS3 — NHS optical voucher.
     * Same criteria as GOS1, with Child patient type as under-19 proxy.
     */
    public function isEligibleGos3(Patient $patient): bool
    {
        $age = $patient->date_of_birth->age;

        if ($age < 16) {
            return true;
        }

        // Child patient type used as proxy for age < 19
        if ($patient->patient_type === PatientType::Child) {
            return true;
        }

        if ($patient->has_glaucoma || $patient->is_diabetic) {
            return true;
        }

        if ($patient->is_blind_partially_sighted || $patient->has_hearing_impairment || $patient->has_retinitis_pigmentosa) {
            return true;
        }

        if ($patient->in_full_time_education && $age < 19) {
            return true;
        }

        if ($this->hasEligibleBenefit($patient)) {
            return true;
        }

        $eligibleTypes = [
            PatientType::Over60,
            PatientType::NHS,
            PatientType::Glaucoma,
            PatientType::FamilyHistory,
        ];

        return in_array($patient->patient_type, $eligibleTypes, strict: true);
    }

    /**
     * GOS6 — domiciliary visit claim.
     * Eligible if the patient qualifies for GOS1 and has a domiciliary reason.
     */
    public function isEligibleGos6(Patient $patient): bool
    {
        return $this->isEligibleGos1($patient) && $patient->domiciliary_reason !== null;
    }

    private function hasEligibleBenefit(Patient $patient): bool
    {
        $benefits = $patient->benefits ?? [];
        return count(array_intersect($benefits, self::ELIGIBLE_BENEFITS)) > 0;
    }
}
