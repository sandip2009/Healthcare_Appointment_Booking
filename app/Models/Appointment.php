<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class Appointment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'healthcare_professional_id',
        'appointment_start_time',
        'appointment_end_time',
        'status',
        'active_status',
        'description',
    ];


    /**
     * Validate appointment request before saving.
     *
     * @throws ValidationException
     */
    public static function validateBooking(array $data)
    {
        //Basic request validation
        $validator = Validator::make($data, [
            'healthcare_professional_id'  => 'required|exists:healthcare_professionals,id',
            'appointment_start_time'      => 'required|date|after:now',
            'appointment_end_time'        => 'required|date|after:appointment_start_time',
            'description'                 => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        //Fetch professional & availability
        $professional = HealthcareProfessional::find($data['healthcare_professional_id']);
        if (!$professional || !$professional->available) {
            throw ValidationException::withMessages([
                'healthcare_professional_id' => ['The selected healthcare professional is not available.'],
            ]);
        }

        //Check day availability
        $availableDays = collect($professional->available_days);
        $startDate = Carbon::parse($data['appointment_start_time']);
        $endDate   = Carbon::parse($data['appointment_end_time']);
        $dayName   = strtoupper($startDate->format('D'));

        $dayInfo = $availableDays->firstWhere('day', $dayName);
        if (!$dayInfo || !$dayInfo['available']) {
            throw ValidationException::withMessages([
                'appointment_start_time' => ["The doctor is not available on {$dayName}."],
            ]);
        }

        // Minimum slot interval like atleast 30 minutes
        $slotIntervalMinutes = $dayInfo['slot_interval_minutes'] ?? 30; // fallback 30 minutes
        if ($slotIntervalMinutes < 30) {
            $slotIntervalMinutes = 30;
        }

        // Check duration
        $durationMinutes = $startDate->diffInMinutes($endDate);
        if ($durationMinutes < $slotIntervalMinutes) {
            throw ValidationException::withMessages([
                'appointment_end_time' => ["The appointment must be at least {$slotIntervalMinutes} minutes long."],
            ]);
        }

        // Optional: Enforce start time alignment with slot interval
        $workStart = $startDate->copy()->setTimeFromTimeString($dayInfo['work_start']);
        $minutesFromStart = $startDate->diffInMinutes($workStart) % $slotIntervalMinutes;
        if ($minutesFromStart !== 0) {
            throw ValidationException::withMessages([
                'appointment_start_time' => ["The appointment start time must align with the {$slotIntervalMinutes}-minute slots."],
            ]);
        }

        // Build working hours on the *same date* as appointment times
        $workStart = $startDate->copy()->setTimeFromTimeString($dayInfo['work_start']);
        $workEnd   = $endDate->copy()->setTimeFromTimeString($dayInfo['work_end']);

        // Validation check
        if ($startDate->lt($workStart) || $endDate->gt($workEnd)) {
            throw ValidationException::withMessages([
                'appointment_start_time' => ["Doctor works only between {$dayInfo['work_start']} and {$dayInfo['work_end']} on {$dayName}."],
            ]);
        }

        //Check overlapping appointments
        $conflict = self::where('healthcare_professional_id', $data['healthcare_professional_id'])
            ->where('status', 'booked')
            ->where(function ($q) use ($data) {
                $q->where('appointment_start_time', '<', $data['appointment_end_time'])
                  ->where('appointment_end_time', '>', $data['appointment_start_time']);
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'appointment_start_time' => ['This time slot is already booked. Please choose another.'],
            ]);
        }

        return $validator->validated();
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'appointment_start_time' => 'datetime',
        'appointment_end_time' => 'datetime',
    ];

    //use in future
    public const STATUS_BOOKED = 'booked';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
}
