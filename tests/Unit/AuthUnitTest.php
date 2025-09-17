<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user()
    {
        $controller = new AuthController();

        $request = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response = $controller->register($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_logs_in_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $controller = new AuthController();

        $request = Request::create('/api/login', 'POST', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response = $controller->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->getData(true)['data'] ?? []);
    }

    /** @test */
    public function it_fails_to_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $controller = new AuthController();

        $request = Request::create('/api/login', 'POST', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response = $controller->login($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Invalid login credentials', $response?->getData(true)['message'] ?? "");
    }
}
