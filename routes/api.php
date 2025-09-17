<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\HealthcareProfessionalController;
use App\Http\Controllers\Api\AppointmentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('healthcare-professionals', [HealthcareProfessionalController::class, 'index']);
    Route::get('healthcare-professionals/{id}', [HealthcareProfessionalController::class, 'show']);

    // Get slots for one specific date parameter
    //id is healthcare professional id and "full_date": "2025-09-16"
    Route::post('/healthcare-professionals/availableSlotsByDate/{id}', [HealthcareProfessionalController::class, 'availableSlotsByDate']);

    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('/appointments/cancel', [AppointmentController::class, 'cancel']);
    Route::post('/appointments/complete', [AppointmentController::class, 'complete']);

});
