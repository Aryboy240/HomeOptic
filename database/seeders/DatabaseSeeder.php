<?php

namespace Database\Seeders;

use App\Models\Diary;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Credentials for the default admin account:
     *   Email:    p.kora@sky.com
     *   Password: password
     */
    public function run(): void
    {
        // Default optometrist / admin account
        User::firstOrCreate(
            ['email' => 'admin@homeoptic.test'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // A default practice so the patient form dropdown is not empty
        Practice::firstOrCreate(
            ['name' => 'HomeOptic (Default Practice)'],
        );

        // A default diary so the appointment calendar has something to show
        Diary::firstOrCreate(
            ['name' => 'Main Diary'],
        );

        $this->call([
            DoctorSeeder::class,
            PctSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
