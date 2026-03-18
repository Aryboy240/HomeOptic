<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum DroppedReason: string
{
    use HasOptions;

    case ChangeOfMind          = 'change_of_mind';
    case HospitalAppointment   = 'hospital_appointment';
    case Illness               = 'illness';
    case NewPatientAwaiting    = 'new_patient_awaiting';
    case NotInAtTimeOfCall     = 'not_in_at_time_of_call';

    public function label(): string
    {
        return match ($this) {
            self::ChangeOfMind        => 'Change of Mind',
            self::HospitalAppointment => 'Hospital Appointment',
            self::Illness             => 'Illness',
            self::NewPatientAwaiting  => 'New Patient Awaiting Eye Test',
            self::NotInAtTimeOfCall   => 'Not In at Time of Call',
        };
    }
}
