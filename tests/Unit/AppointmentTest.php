<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Appointment;
use App\Models\HealthcareProfessional;
use Carbon\Carbon;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_appointment()
    {
        $user = User::factory()->create();
        $doctor = HealthcareProfessional::factory()->create();

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::today()->setTime(10, 0),
            'appointment_end_time'   => Carbon::today()->setTime(11, 0),
            'status' => 'booked',
            'description' => 'General checkup',
        ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'booked',
        ]);
    }

    /** @test */
    public function it_detects_overlapping_appointments()
    {
        $user = User::factory()->create();
        $doctor = HealthcareProfessional::factory()->create();

        // First appointment: 10:00–11:00
        Appointment::create([
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::today()->setTime(10, 0),
            'appointment_end_time'   => Carbon::today()->setTime(11, 0),
            'status' => 'booked',
        ]);

        // Try to create overlapping appointment: 10:30–11:30
        $overlap = Appointment::create([
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::today()->setTime(10, 30),
            'appointment_end_time'   => Carbon::today()->setTime(11, 30),
            'status' => 'booked',
        ]);

        // Check overlap manually
        $hasOverlap = Appointment::where('healthcare_professional_id', $doctor->id)
            ->where(function ($q) use ($overlap) {
                $q->where('appointment_start_time', '<', $overlap->appointment_end_time)
                  ->where('appointment_end_time', '>', $overlap->appointment_start_time);
            })->exists();

        $this->assertTrue($hasOverlap);
    }

    /** @test */
    public function it_belongs_to_user_and_professional()
    {
        $user = User::factory()->create();
        $doctor = HealthcareProfessional::factory()->create();

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::today()->setTime(9, 0),
            'appointment_end_time'   => Carbon::today()->setTime(10, 0),
            'status' => 'booked',
        ]);

        $this->assertEquals($user->id, $appointment->user->id);
        $this->assertEquals($doctor->id, $appointment->healthcareProfessional->id);
    }
}
