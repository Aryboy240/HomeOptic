<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Practice;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $practice = Practice::where('name', 'HomeOptic (Default Practice)')->first();

        $doctors = [
            'Dr. James Smith',
            'Dr. Sarah Patel',
            'Dr. Michael O\'Brien',
            'Dr. Fatima Rahman',
            'Dr. Thomas Hughes',
        ];

        foreach ($doctors as $name) {
            Doctor::firstOrCreate(
                ['name' => $name],
                ['practice_id' => $practice?->id],
            );
        }
    }
}
