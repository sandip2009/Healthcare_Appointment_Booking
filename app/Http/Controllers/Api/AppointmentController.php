<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\HealthcareProfessional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\AppointmentResource;

class AppointmentController extends Controller
{

    /**
     * List appointments (filterable by user).
     */
    public function index(Request $request)
    {
        try {

            // only user's appointments
            $userId = auth()->user()->id;
            $appointments = Appointment::with(['user', 'professional'])
            ->where('user_id', $userId)
            ->where('active_status', 'Y')
            ->latest('appointment_start_time')
            ->paginate(10);

            return (AppointmentResource::collection($appointments))
            ->additional([
                'success' => true,
                'message' => 'Pppointments list fetched successfully.',
            ],200);

        } catch (\Exception $e) {
            // Catch any unexpected error
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while fetching appointments.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Book an appointment with a healthcare professional.
     */
    public function store(Request $request)
    {    

        try {
            $validated = Appointment::validateBooking($request->all());
            
            $userId = auth()->user()->id;

            // Assign appointment to the logged-in user
            $appointment = Appointment::create([
                'user_id' => $userId,
                ...$validated
            ]);

            return response()->json([
                'success'  => true,
                'message'     => 'Appointment booked successfully.',
                'data' => new AppointmentResource($appointment),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Failed to book appointment.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel an appointment (not allowed within 24 hours).
     */
    public function cancel($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            $appointmentDateTime = Carbon::parse($appointment->appointment_start_time);
            $now = Carbon::now();

            if ($now->diffInHours($appointmentDateTime, false) < 24) {
                return response()->json([
                    'success'  => false,
                    'message' => 'You cannot cancel the appointment within 24 hours of the scheduled time.',
                ], 422);
            }

            $appointment->update(['status' => 'cancelled']);

            return response()->json([
                'success'  => true,
                'message' => 'Appointment cancelled successfully.',
                'data' => new AppointmentResource($appointment),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while cancelling the appointment.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific appointment.
     */
    public function show($id)
    {
        try {
            $appointment = Appointment::with(['user', 'professional'])->find($id);

            if (!$appointment) {
                return response()->json([
                    'success'  => true,
                    'message' => 'Appointment not found.',
                    'data' => [],
                ], 200);
            }

            return response()->json([
                'success'  => true,
                'message' => 'Appointment cancelled successfully.',
                'data' => new AppointmentResource($appointment),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Failed to fetch appointment.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function complete($id)
{
    try {
        $appointment = Appointment::findOrFail($id);

        //Prevent marking cancelled or already completed appointments
        if ($appointment->status === 'cancelled') {
            return response()->json([
                'success'  => false,
                'message' => 'Cancelled appointments cannot be marked as completed.',
            ], 422);
        }

        if ($appointment->status === 'completed') {
            return response()->json([
                'success'  => false,
                'message' => 'This appointment is already marked as completed.',
            ], 422);
        }

        // Optionally check if appointment time has passed
        $appointmentDateTime = Carbon::parse($appointment->appointment_end_time);
        if (Carbon::now()->lt($appointmentDateTime)) {
            return response()->json([
                'success'  => false,
                'message' => 'You can only mark the appointment as completed after it has ended.',
            ], 422);
        }

        //Mark as completed
        $appointment->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Appointment marked as completed successfully.',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong while completing the appointment.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
