<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HealthcareProfessional;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthcareProfessional>
 */
class HealthcareProfessionalFactory extends Factory
{
    protected $model = HealthcareProfessional::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $daysOfWeek = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
        $availableDays = collect($daysOfWeek)->map(function ($day) {
            return [
                'day' => $day,
                'available' => $this->faker->boolean(80), // 80% chance available
                'work_start' => '10:00:00',
                'work_end' => '22:00:00',
                'slot_interval_minutes' => 30,
            ];
        })->toArray();

        return [
            'name'       => 'Dr. ' . $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'about'      => $this->faker->sentence(2),
            'available'  => $this->faker->boolean(80), // 80% chance available
            'speciality' => $this->faker->randomElement([
                'GeneralPhysician',
                'Gynecologist',
                'Dermatologist',
                'Pediatricians',
                'Neurologist',
                'Gastroenterologist',
            ]),
            'available_days' => json_encode($availableDays, true), // store as JSON
        ];
    }
    
}