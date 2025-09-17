<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\HealthcareProfessional;
use App\Http\Resources\HealthcareProfessionalResource;

class HealthcareProfessionalController extends Controller
{
    /**
     * Get list of professionals
     */
    public function index()
    {
        try {
            $query = HealthcareProfessional::query()->where('available', true);
            $profs = $query->paginate(10);
            return (HealthcareProfessionalResource::collection($profs))
            ->additional([
                'success' => true,
                'message' => 'Healthcare professionals fetched successfully.',
            ],200);

        } catch (\Exception $e) {
            // Catch any unexpected error
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while fetching healthcare professionals.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $prof = HealthcareProfessional::find($id);

            if (!$prof) {
                return response()->json([
                    'success'  => true,
                    'message' => 'Healthcare professional not found.',
                    'data' => [],
                ], 201);
            }else if (!$prof->available) {
                return response()->json([
                    'success'  => true,
                    'message' => 'This healthcare professional is currently unavailable.',
                    'data' => [],
                ], 201);
            }

            return response()->json([
                'success'  => true,
                'message' => 'Healthcare professionals fetched successfully.',
                'data'    => new HealthcareProfessionalResource($prof),
            ],201);

        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while fetching healthcare professional.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Available booking slots.
     * return slots for a specific date
     */
    public function availableSlotsByDate(Request $request, $id)
    {
         $validated = $request->validate(
            [
                // 'day' => 'required|string|in:MON,TUE,WED,THU,FRI,SAT,SUN',
                // 'date' => 'required|integer|min:1|max:31',
                'full_date' => 'required|date|after_or_equal:today',
                // function ($attribute, $value, $fail) use ($request) {
                //     $dayOfWeek = strtoupper(\Carbon\Carbon::parse($value)->format('D'));
                //     if ($dayOfWeek !== strtoupper($request->day)) {
                //         $fail("The day field does not match the full_date.");
                //     }
                // },
            ],
            [
                // 'full_date.required' => 'Please provide the full date.',
                // 'full_date.date' => 'The full date must be a valid date.',
                'full_date.after_or_equal' => 'The full date must be today or a future date.',
            ]
        );
        try {
            $prof = HealthcareProfessional::findOrFail($id);
            $allDays = $prof->generateBookingSlots();
            $selected = collect($allDays)->firstWhere('full_date', $request->full_date);
            return response()->json([
                'success'  => true,
                'message' => 'Available slots fetched successfully.',
                'full_date'       => $selected['full_date'],
                'slots'           => $selected['slots'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while fetching available slots.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
