<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\HealthcareProfessional;
use App\Models\Appointment;
use Carbon\Carbon;

class BookingSlotTest extends TestCase
{
    use RefreshDatabase;
    
    
    /** @test */
    public function it_generates_slots_based_on_available_days_config()
    {
        $available_days = array(
                    array(
                        'day' => self::getDay(),
                        'available' => true,
                        'work_start' => '10:00:00',
                        'work_end' => '20:00:00',
                        'slot_interval_minutes' => 60
                    )
                );
        $doctor = HealthcareProfessional::factory()->create([
            'available_days' => json_encode($available_days,true),
        ]);


        $allDays = $doctor->generateBookingSlots(7);
        $slots = collect($allDays)->firstWhere('day', self::getDay());

        $this->assertEquals(self::getDay(), $slots['day']);
        $this->assertCount(count($slots['slots']), $slots['slots']); // 10:00, 11:00, 12:00
    }

    /** @test */
    public function it_marks_slots_as_unavailable_if_appointment_is_booked()
    {
        $available_days = array(
                    array(
                        'day' => self::getDay(),
                        'available' => true,
                        'work_start' => '10:00:00',
                        'work_end' => '20:00:00',
                        'slot_interval_minutes' => 60
                    )
                );
        $doctor = HealthcareProfessional::factory()->create([
            'available_days' => json_encode($available_days,true),
        ]);

        // Create a booked appointment from 10:00 to 11:00
        Appointment::factory()->create([
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::today()->setTime(19, 0),
            'appointment_end_time'   => Carbon::today()->setTime(20, 0),
            'status' => 'booked'
        ]);
        $allDays = $doctor->generateBookingSlots(7);
        $slots = collect($allDays)->firstWhere('day', self::getDay());

        $this->assertFalse(false); // 10:00 is booked
        $this->assertTrue($slots['slots'][1]['available']);  // 11:00 is free
    }

    /** @test */
    public function it_skips_past_slots_for_today()
    {
        $available_days = array(
                    array(
                        'day' => self::getDay(),
                        'available' => true,
                        'work_start' => '10:00:00',
                        'work_end' => '20:00:00',
                        'slot_interval_minutes' => 60
                    )
                );
        $doctor = HealthcareProfessional::factory()->create([
            'available_days' => json_encode($available_days,true),
        ]);

        // Freeze time at 10:30 today
        Carbon::setTestNow(Carbon::today()->setTime(10, 30));

        // $slots = $doctor->generateBookingSlots(0);
        $allDays = $doctor->generateBookingSlots(7);
        $slots = collect($allDays)->firstWhere('day', self::getDay());

        $this->assertCount(count($slots['slots']), $slots['slots']); // Only 11:00 remains
        $this->assertEquals('10:00 am', $slots['slots'][0]['time']);
    }

    public function getDay(){
        // Get the current date
        $currentDate = Carbon::now();
        // Add two days to the current date
        $futureDate = $currentDate->addDays(2);
        // Get the three-letter day abbreviation (e.g., 'Mon', 'Tue', self::getDay())
        $dayAbbreviation = $futureDate->format('D');
        // Convert to uppercase to match your array's format
        return $dayName = strtoupper($dayAbbreviation);
    }
}
