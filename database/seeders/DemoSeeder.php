<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Diary;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Practice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $practice = Practice::where('name', 'HomeOptic (Default Practice)')->firstOrFail();
        $diary    = Diary::where('name', 'Main Diary')->firstOrFail();

        $doctors = Doctor::pluck('id', 'name');

        // ── 10 patients ───────────────────────────────────────────────────────

        $patientData = [
            // 1 — Margaret Williams (domiciliary: arthritis)
            [
                'key'                    => 'Williams',
                'title'                  => 'Mrs',
                'first_name'             => 'Margaret',
                'surname'                => 'Williams',
                'sex_gender'             => 'female',
                'date_of_birth'          => '1948-03-15',
                'address_line_1'         => '14 Maple Avenue',
                'town_city'              => 'Birmingham',
                'county'                 => 'West Midlands',
                'post_code'              => 'B15 2TT',
                'country'                => 'England',
                'telephone_mobile'       => '07712 334 521',
                'patient_type'           => 'B',
                'is_nhs'                 => false,
                'is_diabetic'            => true,
                'has_glaucoma'           => false,
                'domiciliary_reason'     => 'arthritis',
                'physical_disabilities'  => 'Severe arthritis in both knees and hips — unable to walk more than a few steps unaided',
                'in_full_time_education' => false,
                'benefits'               => ['income_support'],
                'next_of_kin_name'       => 'David Williams',
                'next_of_kin_relationship' => 'Son',
                'next_of_kin_phone'      => '07712 345 678',
                'doctor'                 => 'Dr. James Smith',
            ],
            // 2 — Rajesh Patel (domiciliary: alzheimers)
            [
                'key'                      => 'RPatel',
                'title'                    => 'Mr',
                'first_name'               => 'Rajesh',
                'surname'                  => 'Patel',
                'sex_gender'               => 'male',
                'date_of_birth'            => '1941-07-22',
                'address_line_1'           => '8 Birchwood Close',
                'town_city'                => 'Leicester',
                'county'                   => 'Leicestershire',
                'post_code'                => 'LE3 6HN',
                'country'                  => 'England',
                'telephone_mobile'         => '07723 456 789',
                'patient_type'             => 'B',
                'is_nhs'                   => false,
                'is_diabetic'              => false,
                'has_glaucoma'             => true,
                'domiciliary_reason'       => 'alzheimers',
                'mental_health_conditions' => "Moderate Alzheimer's disease — requires full-time carer",
                'carer_name'               => 'Priya Patel',
                'carer_phone'              => '07723 456 789',
                'next_of_kin_name'         => 'Priya Patel',
                'next_of_kin_relationship' => 'Daughter',
                'next_of_kin_phone'        => '07723 456 789',
                'doctor'                   => 'Dr. Sarah Patel',
            ],
            // 3 — Dorothy Marsh (domiciliary: angina)
            [
                'key'                   => 'Marsh',
                'title'                 => 'Mrs',
                'first_name'            => 'Dorothy',
                'surname'               => 'Marsh',
                'sex_gender'            => 'female',
                'date_of_birth'         => '1952-11-03',
                'address_line_1'        => '32 Oak Street',
                'town_city'             => 'Manchester',
                'county'                => 'Greater Manchester',
                'post_code'             => 'M21 9DP',
                'country'               => 'England',
                'telephone_other'       => '0161 445 8821',
                'patient_type'          => 'B',
                'is_nhs'                => false,
                'is_diabetic'           => false,
                'has_glaucoma'          => true,
                'domiciliary_reason'    => 'angina',
                'physical_disabilities' => 'Angina — exertion causes chest pain, cannot travel unaccompanied',
                'benefits'              => ['pension_credit'],
                'next_of_kin_name'      => 'Robert Marsh',
                'next_of_kin_relationship' => 'Husband',
                'next_of_kin_phone'     => '07734 567 890',
                'doctor'                => 'Dr. Fatima Rahman',
            ],
            // 4 — Patricia Donnelly (domiciliary: agoraphobic)
            [
                'key'                      => 'Donnelly',
                'title'                    => 'Mrs',
                'first_name'               => 'Patricia',
                'surname'                  => 'Donnelly',
                'sex_gender'               => 'female',
                'date_of_birth'            => '1950-03-31',
                'address_line_1'           => '91 Highfield Road',
                'town_city'                => 'Liverpool',
                'county'                   => 'Merseyside',
                'post_code'                => 'L15 3HU',
                'country'                  => 'England',
                'telephone_mobile'         => '07745 678 901',
                'patient_type'             => 'B',
                'is_nhs'                   => false,
                'is_diabetic'              => true,
                'has_glaucoma'             => false,
                'domiciliary_reason'       => 'agoraphobic',
                'mental_health_conditions' => 'Severe agoraphobia — housebound, unable to leave without significant distress',
                'benefits'                 => ['universal_credit'],
                'carer_name'               => 'Sean Donnelly',
                'carer_phone'              => '07745 678 901',
                'doctor'                   => 'Dr. Thomas Hughes',
            ],
            // 5 — James Thornton (domiciliary: diabetic retinopathy / partially sighted)
            [
                'key'                        => 'Thornton',
                'title'                      => 'Mr',
                'first_name'                 => 'James',
                'surname'                    => 'Thornton',
                'sex_gender'                 => 'male',
                'date_of_birth'              => '1944-06-14',
                'address_line_1'             => '5 Station Road',
                'town_city'                  => 'Bristol',
                'county'                     => 'Avon',
                'post_code'                  => 'BS3 4LW',
                'country'                    => 'England',
                'telephone_mobile'           => '07756 789 012',
                'patient_type'               => 'B',
                'is_nhs'                     => false,
                'is_diabetic'                => true,
                'has_glaucoma'               => false,
                'is_blind_partially_sighted' => true,
                'physical_disabilities'      => 'Registered partially sighted — diabetic retinopathy',
                'benefits'                   => ['pension_credit', 'hc2_certificate'],
                'next_of_kin_name'           => 'Susan Thornton',
                'next_of_kin_relationship'   => 'Wife',
                'next_of_kin_phone'          => '07756 789 012',
                'doctor'                     => "Dr. Michael O'Brien",
            ],
            // 6 — Eleanor Hughes (NHS, hearing impairment)
            [
                'key'                    => 'Hughes',
                'title'                  => 'Mrs',
                'first_name'             => 'Eleanor',
                'surname'                => 'Hughes',
                'sex_gender'             => 'female',
                'date_of_birth'          => '1958-09-28',
                'address_line_1'         => '17 Park Lane',
                'town_city'              => 'Leeds',
                'county'                 => 'West Yorkshire',
                'post_code'              => 'LS6 2NR',
                'country'                => 'England',
                'telephone_mobile'       => '07767 890 123',
                'patient_type'           => 'C',
                'is_nhs'                 => true,
                'has_hearing_impairment' => true,
                'benefits'               => ['income_support', 'hc2_certificate'],
                'next_of_kin_name'       => 'Thomas Hughes',
                'next_of_kin_relationship' => 'Brother',
                'next_of_kin_phone'      => '07767 890 123',
                'doctor'                 => 'Dr. Thomas Hughes',
            ],
            // 7 — Mohammed Al-Rashid (NHS, JSA)
            [
                'key'            => 'AlRashid',
                'title'          => 'Mr',
                'first_name'     => 'Mohammed',
                'surname'        => 'Al-Rashid',
                'sex_gender'     => 'male',
                'date_of_birth'  => '1985-02-05',
                'address_line_1' => '44 Victoria Street',
                'town_city'      => 'Sheffield',
                'county'         => 'South Yorkshire',
                'post_code'      => 'S1 3GH',
                'country'        => 'England',
                'telephone_mobile' => '07778 901 234',
                'patient_type'   => 'C',
                'is_nhs'         => true,
                'benefits'       => ['jobseekers_allowance'],
                'doctor'         => 'Dr. James Smith',
            ],
            // 8 — Claire Brennan (Private)
            [
                'key'            => 'Brennan',
                'title'          => 'Miss',
                'first_name'     => 'Claire',
                'surname'        => 'Brennan',
                'sex_gender'     => 'female',
                'date_of_birth'  => '1991-12-19',
                'address_line_1' => '2 The Avenue',
                'town_city'      => 'Oxford',
                'county'         => 'Oxfordshire',
                'post_code'      => 'OX2 6AH',
                'country'        => 'England',
                'telephone_mobile' => '07789 012 345',
                'email'          => 'claire.brennan@email.co.uk',
                'patient_type'   => 'A',
                'is_nhs'         => false,
                'doctor'         => 'Dr. Sarah Patel',
            ],
            // 9 — William Foster (domiciliary: alzheimers / advanced dementia)
            [
                'key'                      => 'Foster',
                'title'                    => 'Mr',
                'first_name'               => 'William',
                'surname'                  => 'Foster',
                'sex_gender'               => 'male',
                'date_of_birth'            => '1939-08-07',
                'address_line_1'           => '1 Elmwood Drive',
                'town_city'                => 'Nottingham',
                'county'                   => 'Nottinghamshire',
                'post_code'                => 'NG5 2PQ',
                'country'                  => 'England',
                'telephone_other'          => '0115 960 1122',
                'patient_type'             => 'B',
                'is_nhs'                   => false,
                'has_glaucoma'             => true,
                'domiciliary_reason'       => 'alzheimers',
                'mental_health_conditions' => 'Advanced dementia — requires full supervision',
                'benefits'                 => ['pension_credit'],
                'carer_name'               => 'NHS Care Home Staff',
                'doctor'                   => 'Dr. Fatima Rahman',
            ],
            // 10 — Sarah Kaur (NHS, diabetic)
            [
                'key'            => 'Kaur',
                'title'          => 'Mrs',
                'first_name'     => 'Sarah',
                'surname'        => 'Kaur',
                'sex_gender'     => 'female',
                'date_of_birth'  => '1972-04-14',
                'address_line_1' => '78 Chestnut Grove',
                'town_city'      => 'Coventry',
                'county'         => 'West Midlands',
                'post_code'      => 'CV3 5RT',
                'country'        => 'England',
                'telephone_mobile' => '07890 123 456',
                'email'          => 'sarah.kaur@email.co.uk',
                'patient_type'   => 'C',
                'is_nhs'         => true,
                'is_diabetic'    => true,
                'benefits'       => ['universal_credit'],
                'doctor'         => 'Dr. James Smith',
            ],
        ];

        $patients = [];
        foreach ($patientData as $data) {
            $doctorId = $doctors[$data['doctor']] ?? null;

            $patient = Patient::firstOrCreate(
                ['first_name' => $data['first_name'], 'surname' => $data['surname']],
                [
                    'title'                      => $data['title'],
                    'sex_gender'                 => $data['sex_gender'],
                    'date_of_birth'              => $data['date_of_birth'],
                    'address_line_1'             => $data['address_line_1'],
                    'town_city'                  => $data['town_city'],
                    'county'                     => $data['county'] ?? null,
                    'post_code'                  => $data['post_code'],
                    'country'                    => $data['country'] ?? null,
                    'telephone_mobile'           => $data['telephone_mobile'] ?? null,
                    'telephone_other'            => $data['telephone_other'] ?? null,
                    'email'                      => $data['email'] ?? null,
                    'patient_type'               => $data['patient_type'],
                    'is_nhs'                     => $data['is_nhs'] ?? false,
                    'is_diabetic'                => $data['is_diabetic'] ?? false,
                    'has_glaucoma'               => $data['has_glaucoma'] ?? false,
                    'is_blind_partially_sighted' => $data['is_blind_partially_sighted'] ?? false,
                    'has_hearing_impairment'     => $data['has_hearing_impairment'] ?? false,
                    'has_retinitis_pigmentosa'   => $data['has_retinitis_pigmentosa'] ?? false,
                    'domiciliary_reason'         => $data['domiciliary_reason'] ?? null,
                    'physical_disabilities'      => $data['physical_disabilities'] ?? null,
                    'mental_health_conditions'   => $data['mental_health_conditions'] ?? null,
                    'in_full_time_education'     => $data['in_full_time_education'] ?? false,
                    'benefits'                   => $data['benefits'] ?? null,
                    'next_of_kin_name'           => $data['next_of_kin_name'] ?? null,
                    'next_of_kin_relationship'   => $data['next_of_kin_relationship'] ?? null,
                    'next_of_kin_phone'          => $data['next_of_kin_phone'] ?? null,
                    'carer_name'                 => $data['carer_name'] ?? null,
                    'carer_phone'                => $data['carer_phone'] ?? null,
                    'practice_id'                => $practice->id,
                    'doctor_id'                  => $doctorId,
                    'status'                     => 'active',
                ]
            );

            $patients[$data['key']] = $patient;
        }

        // ── 15 appointments (Mon–Fri current week) ────────────────────────────
        //
        // Domiciliary (60 min) for patients 1,2,3,4,5,9
        // Routine eye test (30 min) for patients 6,7,8,10
        //
        //   Mon: 09:00–10:00 domiciliary(Williams), 10:30–11:30 domiciliary(RPatel), 14:00–14:30 routine(Hughes)
        //   Tue: 09:00–10:00 domiciliary(Marsh),    11:00–11:30 routine(AlRashid),   14:00–14:30 routine(Brennan)
        //   Wed: 09:00–10:00 domiciliary(Donnelly), 10:30–11:30 domiciliary(Thornton), 14:00–14:30 routine(Kaur)
        //   Thu: 09:00–10:00 domiciliary(Foster),   10:30–11:00 routine(Hughes),     13:00–13:30 routine(AlRashid)
        //   Fri: 09:00–10:00 domiciliary(Williams), 11:00–11:30 routine(Kaur),       14:30–15:00 routine(Brennan)

        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY)->startOfDay();

        $appointmentData = [
            // Monday
            ['offset' => 0, 'start' => '09:00', 'length' => 60, 'type' => 'domiciliary',      'status' => 'completed', 'key' => 'Williams'],
            ['offset' => 0, 'start' => '10:30', 'length' => 60, 'type' => 'domiciliary',      'status' => 'completed', 'key' => 'RPatel'],
            ['offset' => 0, 'start' => '14:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'completed', 'key' => 'Hughes'],
            // Tuesday
            ['offset' => 1, 'start' => '09:00', 'length' => 60, 'type' => 'domiciliary',      'status' => 'completed', 'key' => 'Marsh'],
            ['offset' => 1, 'start' => '11:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'completed', 'key' => 'AlRashid'],
            ['offset' => 1, 'start' => '14:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'key' => 'Brennan'],
            // Wednesday
            ['offset' => 2, 'start' => '09:00', 'length' => 60, 'type' => 'domiciliary',      'status' => 'completed', 'key' => 'Donnelly'],
            ['offset' => 2, 'start' => '10:30', 'length' => 60, 'type' => 'domiciliary',      'status' => 'completed', 'key' => 'Thornton'],
            ['offset' => 2, 'start' => '14:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'confirmed', 'key' => 'Kaur'],
            // Thursday
            ['offset' => 3, 'start' => '09:00', 'length' => 60, 'type' => 'domiciliary',      'status' => 'confirmed', 'key' => 'Foster'],
            ['offset' => 3, 'start' => '10:30', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'key' => 'Hughes'],
            ['offset' => 3, 'start' => '13:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'key' => 'AlRashid'],
            // Friday
            ['offset' => 4, 'start' => '09:00', 'length' => 60, 'type' => 'domiciliary',      'status' => 'confirmed', 'key' => 'Williams'],
            ['offset' => 4, 'start' => '11:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'key' => 'Kaur'],
            ['offset' => 4, 'start' => '14:30', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'key' => 'Brennan'],
        ];

        foreach ($appointmentData as $appt) {
            $date    = $monday->copy()->addDays($appt['offset'])->toDateString();
            $patient = $patients[$appt['key']];

            Appointment::firstOrCreate(
                [
                    'diary_id'   => $diary->id,
                    'date'       => $date,
                    'start_time' => $appt['start'],
                ],
                [
                    'patient_id'         => $patient->id,
                    'length_minutes'     => $appt['length'],
                    'appointment_type'   => $appt['type'],
                    'appointment_status' => $appt['status'],
                ]
            );
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $patientCount     = Patient::count();
        $appointmentCount = Appointment::count();
        $this->command->info("Seeded: {$patientCount} patients, {$appointmentCount} appointments.");
    }
}
