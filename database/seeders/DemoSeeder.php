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

        // ── 10 filler patients ────────────────────────────────────────────────

        $patientData = [
            [
                'title'              => 'Mrs',
                'first_name'         => 'Margaret',
                'surname'            => 'Williams',
                'sex_gender'         => 'female',
                'date_of_birth'      => '1945-06-15',
                'address_line_1'     => '14 Hawthorn Close',
                'town_city'          => 'Birmingham',
                'county'             => 'West Midlands',
                'post_code'          => 'B15 2TH',
                'country'            => 'England',
                'telephone_mobile'   => '07712 334 521',
                'patient_type'       => 'B',      // Over 60
                'is_nhs'             => false,
                'is_diabetic'        => true,
                'has_glaucoma'       => false,
                'domiciliary_reason' => 'arthritis',
                'doctor'             => 'Dr. James Smith',
            ],
            [
                'title'              => 'Mr',
                'first_name'         => 'Robert',
                'surname'            => 'Johnson',
                'sex_gender'         => 'male',
                'date_of_birth'      => '1958-03-22',
                'address_line_1'     => '3 Maple Avenue',
                'town_city'          => 'Leeds',
                'county'             => 'West Yorkshire',
                'post_code'          => 'LS6 4DQ',
                'country'            => 'England',
                'telephone_mobile'   => '07934 112 788',
                'patient_type'       => 'B',      // Over 60
                'is_nhs'             => false,
                'is_diabetic'        => false,
                'has_glaucoma'       => false,
                'domiciliary_reason' => null,
                'doctor'             => 'Dr. Sarah Patel',
            ],
            [
                'title'              => 'Mrs',
                'first_name'         => 'Dorothy',
                'surname'            => 'Patel',
                'sex_gender'         => 'female',
                'date_of_birth'      => '1940-11-08',
                'address_line_1'     => '27 Orchard Street',
                'town_city'          => 'Manchester',
                'county'             => 'Greater Manchester',
                'post_code'          => 'M14 5RP',
                'country'            => 'England',
                'telephone_other'    => '0161 445 8821',
                'patient_type'       => 'C',      // NHS
                'is_nhs'             => true,
                'is_diabetic'        => false,
                'has_glaucoma'       => true,
                'domiciliary_reason' => 'alzheimers',
                'doctor'             => 'Dr. Fatima Rahman',
            ],
            [
                'title'              => 'Mr',
                'first_name'         => 'David',
                'surname'            => 'Clarke',
                'sex_gender'         => 'male',
                'date_of_birth'      => '1975-09-14',
                'address_line_1'     => '8 Birchwood Road',
                'town_city'          => 'Bristol',
                'county'             => 'Avon',
                'post_code'          => 'BS7 9JN',
                'country'            => 'England',
                'telephone_mobile'   => '07854 667 203',
                'email'              => 'david.clarke@email.co.uk',
                'patient_type'       => 'A',      // Private
                'is_nhs'             => false,
                'is_diabetic'        => false,
                'has_glaucoma'       => false,
                'domiciliary_reason' => null,
                'doctor'             => 'Dr. James Smith',
            ],
            [
                'title'              => 'Mrs',
                'first_name'         => 'Susan',
                'surname'            => 'Thornton',
                'sex_gender'         => 'female',
                'date_of_birth'      => '1962-04-27',
                'address_line_1'     => '55 Victoria Road',
                'town_city'          => 'Sheffield',
                'county'             => 'South Yorkshire',
                'post_code'          => 'S10 2DG',
                'country'            => 'England',
                'telephone_mobile'   => '07701 998 344',
                'patient_type'       => 'B',      // Over 60
                'is_nhs'             => false,
                'is_diabetic'        => true,
                'has_glaucoma'       => false,
                'domiciliary_reason' => null,
                'doctor'             => "Dr. Michael O'Brien",
            ],
            [
                'title'              => 'Mr',
                'first_name'         => 'Peter',
                'surname'            => 'Whitfield',
                'sex_gender'         => 'male',
                'date_of_birth'      => '1994-01-30',
                'address_line_1'     => '12 Canal Street',
                'town_city'          => 'Nottingham',
                'county'             => 'Nottinghamshire',
                'post_code'          => 'NG1 7AE',
                'country'            => 'England',
                'telephone_mobile'   => '07423 561 879',
                'email'              => 'pwhitfield94@outlook.com',
                'patient_type'       => 'C',      // NHS
                'is_nhs'             => true,
                'is_diabetic'        => false,
                'has_glaucoma'       => false,
                'domiciliary_reason' => null,
                'doctor'             => 'Dr. Thomas Hughes',
            ],
            [
                'title'              => 'Mrs',
                'first_name'         => 'Eileen',
                'surname'            => 'Marsh',
                'sex_gender'         => 'female',
                'date_of_birth'      => '1943-07-19',
                'address_line_1'     => '6 Primrose Lane',
                'town_city'          => 'Coventry',
                'county'             => 'West Midlands',
                'post_code'          => 'CV3 2BQ',
                'country'            => 'England',
                'telephone_other'    => '024 7665 3210',
                'patient_type'       => 'B',      // Over 60
                'is_nhs'             => false,
                'is_diabetic'        => false,
                'has_glaucoma'       => true,
                'domiciliary_reason' => 'angina',
                'doctor'             => 'Dr. James Smith',
            ],
            [
                'title'              => 'Mr',
                'first_name'         => 'James',
                'surname'            => 'Hartley',
                'sex_gender'         => 'male',
                'date_of_birth'      => '1968-12-05',
                'address_line_1'     => '39 Churchill Way',
                'town_city'          => 'Leicester',
                'county'             => 'Leicestershire',
                'post_code'          => 'LE2 7PN',
                'country'            => 'England',
                'telephone_mobile'   => '07890 223 567',
                'patient_type'       => 'C',      // NHS
                'is_nhs'             => true,
                'is_diabetic'        => false,
                'has_glaucoma'       => false,
                'domiciliary_reason' => null,
                'doctor'             => 'Dr. Sarah Patel',
            ],
            [
                'title'              => 'Mrs',
                'first_name'         => 'Patricia',
                'surname'            => 'Donnelly',
                'sex_gender'         => 'female',
                'date_of_birth'      => '1950-03-31',
                'address_line_1'     => '91 Highfield Road',
                'town_city'          => 'Liverpool',
                'county'             => 'Merseyside',
                'post_code'          => 'L15 3HU',
                'country'            => 'England',
                'telephone_mobile'   => '07567 441 882',
                'patient_type'       => 'B',      // Over 60
                'is_nhs'             => false,
                'is_diabetic'        => true,
                'has_glaucoma'       => false,
                'domiciliary_reason' => 'agoraphobic',
                'doctor'             => 'Dr. Thomas Hughes',
            ],
            [
                'title'              => 'Mr',
                'first_name'         => 'Kevin',
                'surname'            => 'McAllister',
                'sex_gender'         => 'male',
                'date_of_birth'      => '1989-08-12',
                'address_line_1'     => '22 Queensway',
                'town_city'          => 'Edinburgh',
                'county'             => null,
                'post_code'          => 'EH2 4HJ',
                'country'            => 'Scotland',
                'telephone_mobile'   => '07311 774 090',
                'email'              => 'k.mcallister@gmail.com',
                'patient_type'       => 'A',      // Private
                'is_nhs'             => false,
                'is_diabetic'        => false,
                'has_glaucoma'       => false,
                'domiciliary_reason' => null,
                'doctor'             => "Dr. Michael O'Brien",
            ],
        ];

        $patients = [];
        foreach ($patientData as $data) {
            $doctorId = $doctors[$data['doctor']] ?? null;

            $patient = Patient::firstOrCreate(
                ['first_name' => $data['first_name'], 'surname' => $data['surname']],
                [
                    'title'              => $data['title'],
                    'sex_gender'         => $data['sex_gender'],
                    'date_of_birth'      => $data['date_of_birth'],
                    'address_line_1'     => $data['address_line_1'],
                    'town_city'          => $data['town_city'],
                    'county'             => $data['county'] ?? null,
                    'post_code'          => $data['post_code'],
                    'country'            => $data['country'] ?? null,
                    'telephone_mobile'   => $data['telephone_mobile'] ?? null,
                    'telephone_other'    => $data['telephone_other'] ?? null,
                    'email'              => $data['email'] ?? null,
                    'patient_type'       => $data['patient_type'],
                    'is_nhs'             => $data['is_nhs'],
                    'is_diabetic'        => $data['is_diabetic'],
                    'has_glaucoma'       => $data['has_glaucoma'],
                    'domiciliary_reason' => $data['domiciliary_reason'],
                    'practice_id'        => $practice->id,
                    'doctor_id'          => $doctorId,
                    'status'             => 'active',
                ]
            );

            $patients[$data['surname']] = $patient;
        }

        // ── 15 filler appointments (Mon–Fri of the current week) ──────────────
        //
        // 3 per day, no overlaps. Times verified manually:
        //   Mon: 09:00–09:30, 10:30–11:15, 14:00–14:30
        //   Tue: 09:00–09:45, 11:00–11:30, 15:00–15:30
        //   Wed: 09:30–10:00, 11:00–11:45, 14:00–14:30
        //   Thu: 09:00–09:30, 10:30–11:15, 13:00–13:30
        //   Fri: 09:00–09:45, 11:00–11:30, 14:30–15:00

        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY)->startOfDay();

        $appointmentData = [
            // Monday
            ['offset' => 0, 'start' => '09:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'completed', 'patient' => 'Clarke'],
            ['offset' => 0, 'start' => '10:30', 'length' => 45, 'type' => 'domiciliary',      'status' => 'completed', 'patient' => 'Williams'],
            ['offset' => 0, 'start' => '14:00', 'length' => 30, 'type' => 'follow_up',        'status' => 'completed', 'patient' => 'Hartley'],
            // Tuesday
            ['offset' => 1, 'start' => '09:00', 'length' => 45, 'type' => 'domiciliary',      'status' => 'completed', 'patient' => 'Patel'],
            ['offset' => 1, 'start' => '11:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'completed', 'patient' => 'Whitfield'],
            ['offset' => 1, 'start' => '15:00', 'length' => 30, 'type' => 'follow_up',        'status' => 'booked',    'patient' => 'McAllister'],
            // Wednesday
            ['offset' => 2, 'start' => '09:30', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'completed', 'patient' => 'Thornton'],
            ['offset' => 2, 'start' => '11:00', 'length' => 45, 'type' => 'domiciliary',      'status' => 'completed', 'patient' => 'Marsh'],
            ['offset' => 2, 'start' => '14:00', 'length' => 30, 'type' => 'contact_lens',     'status' => 'confirmed', 'patient' => 'Johnson'],
            // Thursday
            ['offset' => 3, 'start' => '09:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'patient' => 'McAllister'],
            ['offset' => 3, 'start' => '10:30', 'length' => 45, 'type' => 'domiciliary',      'status' => 'confirmed', 'patient' => 'Donnelly'],
            ['offset' => 3, 'start' => '13:00', 'length' => 30, 'type' => 'follow_up',        'status' => 'booked',    'patient' => 'Clarke'],
            // Friday
            ['offset' => 4, 'start' => '09:00', 'length' => 45, 'type' => 'domiciliary',      'status' => 'confirmed', 'patient' => 'Patel'],
            ['offset' => 4, 'start' => '11:00', 'length' => 30, 'type' => 'routine_eye_test', 'status' => 'booked',    'patient' => 'Williams'],
            ['offset' => 4, 'start' => '14:30', 'length' => 30, 'type' => 'follow_up',        'status' => 'booked',    'patient' => 'Johnson'],
        ];

        foreach ($appointmentData as $appt) {
            $date    = $monday->copy()->addDays($appt['offset'])->toDateString();
            $patient = $patients[$appt['patient']];

            Appointment::firstOrCreate(
                [
                    'diary_id'   => $diary->id,
                    'date'       => $date,
                    'start_time' => $appt['start'],
                ],
                [
                    'patient_id'          => $patient->id,
                    'length_minutes'      => $appt['length'],
                    'appointment_type'    => $appt['type'],
                    'appointment_status'  => $appt['status'],
                ]
            );
        }
    }
}
