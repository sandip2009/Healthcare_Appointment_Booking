<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthcareProfessionalController;
use App\Http\Controllers\Api\AppointmentController;
use App\Models\User;
use App\Models\HealthcareProfessional;
use App\Models\Appointment;
use Carbon\Carbon;

class ApiUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $authController;
    protected $doctorController;
    protected $appointmentController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController();
        $this->doctorController = new HealthcareProfessionalController();
        $this->appointmentController = new AppointmentController();
    }

    /** @test */
    public function user_can_register_and_login()
    {
        // Register
        $registerRequest = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse = $this->authController->register($registerRequest);
        $this->assertEquals(200, $registerResponse->getStatusCode());
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        // Login
        $loginRequest = Request::create('/api/login', 'POST', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $loginResponse = $this->authController->login($loginRequest);
        $this->assertEquals(200, $loginResponse->getStatusCode());
        $this->assertArrayHasKey('token', $loginResponse->getData(true)['data'] ?? []);
    }

    /** @test */
    public function it_returns_healthcare_professionals()
    {
        // HealthcareProfessional::factory()->count(2)->create();
        $available_days = array(
                    array(
                        'day' => self::getDay(),
                        'available' => true,
                        'work_start' => '10:00:00',
                        'work_end' => '20:00:00',
                        'slot_interval_minutes' => 60
                    )
                ); 
        HealthcareProfessional::factory()->create([
            'available_days' => json_encode($available_days,true),
        ]);

        $request = Request::create('/api/healthcare-professionals', 'GET');
        $response = $this->doctorController->index($request);
        // dd($response->getStatusCode(), $response->getData(true)['data']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getData(true) ?? []);
    }

    /** @test */
    public function user_can_book_an_appointment()
    {
        $user = User::factory()->create();
        // dd($doctor->toArray());
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

        $request = Request::create('/api/appointments', 'POST', [
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::tomorrow()->setTime(19, 0),
            'appointment_end_time'   => Carbon::tomorrow()->setTime(20, 0),
            'description' => 'General checkup',
        ]);

        $response = $this->appointmentController->store($request);
        // dd($response->getStatusCode(), $response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('appointments', [
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
        ]);
    }

    /** @test */
    public function it_shows_an_appointment_details()
    {
        $user = User::factory()->create();
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

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::tomorrow()->setTime(9, 0),
            'appointment_end_time'   => Carbon::tomorrow()->setTime(10, 0),
            'status' => 'booked',
        ]);

        $response = $this->appointmentController->show($appointment->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($appointment->id, $response->getData(true)['data']['id']);
    }

    /** @test */
    public function user_can_cancel_an_appointment()
    {
        $user = User::factory()->create();
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

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'healthcare_professional_id' => $doctor->id,
            'appointment_start_time' => Carbon::tomorrow()->setTime(19, 0),
            'appointment_end_time'   => Carbon::tomorrow()->setTime(20, 0),
            'status' => 'booked',
            'active_status' => 'Y',
        ]);
        // dd($appointment->toArray());

        $request = Request::create('/api/appointments/cancel', 'POST', [
            'appointment_id' => $appointment->id,
            'user_id' => $user->id,
        ]);
        // dd($request->all());
        $response = $this->appointmentController->cancel($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('cancelled', $appointment->fresh()->status);
    }
    public function getDay(){
        // Get the current date
        $currentDate = Carbon::now();
        // Add two days to the current date
        $futureDate = $currentDate->addDays(1);
        // Get the three-letter day abbreviation (e.g., 'Mon', 'Tue', self::getDay())
        $dayAbbreviation = $futureDate->format('D');
        // Convert to uppercase to match your array's format
        return $dayName = strtoupper($dayAbbreviation);
    }
}
