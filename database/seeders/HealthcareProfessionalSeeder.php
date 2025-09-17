<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\HealthcareProfessional;


class HealthcareProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $specialities = [
            'GeneralPhysician',
            'Gynecologist',
            'Dermatologist',
            'Pediatricians',
            'Neurologist',
            'Gastroenterologist',
        ];

        foreach ($specialities as $speciality) {
            HealthcareProfessional::factory()->create([
                'speciality' => $speciality,
            ]);
        }
    }
}
