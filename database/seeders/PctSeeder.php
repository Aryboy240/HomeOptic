<?php

namespace Database\Seeders;

use App\Models\Pct;
use Illuminate\Database\Seeder;

class PctSeeder extends Seeder
{
    public function run(): void
    {
        $pcts = [
            'Aintree University Hospital',
            'Airedale NHS Foundation Trust',
            'Alder Hey Children\'s NHS Foundation Trust',
            'Ashford and St Peter\'s Hospitals NHS Foundation Trust',
            'Barking, Havering and Redbridge University Hospitals',
            'Barnsley Hospital NHS Foundation Trust',
            'Barts Health NHS Trust',
            'Basildon and Thurrock University Hospitals',
        ];

        foreach ($pcts as $name) {
            Pct::firstOrCreate(['name' => $name]);
        }
    }
}
