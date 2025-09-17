<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\HealthcareProfessional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $start = Carbon::today()->setTime(10, 0);
        $end   = (clone $start)->addMinutes(30);

        return [
            'user_id' => User::factory(),
            'healthcare_professional_id' => HealthcareProfessional::factory(),
            'appointment_start_time' => $start,
            'appointment_end_time'   => $end,
            'status' => 'booked',
            'active_status' => 'Y',
            'description' => $this->faker->sentence(),
        ];
    }
}
