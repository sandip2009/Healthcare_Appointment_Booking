<?php
namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Appointment;


trait HasBookingSlots
{
    /**
     * Return base time slots as strings formatted 'h:i a'
     * Uses in-memory static cache for the current request to avoid recomputation.
     */
    protected function getBaseTimeSlots(string $start, string $end, int $intervalMinutes): array
    {
        static $mem = [];
        $key = "{$start}_{$end}_{$intervalMinutes}";

        if (isset($mem[$key])) return $mem[$key];

        // Optionally, persistent caching:
        // $mem[$key] = Cache::remember("base_slots_{$key}", 3600, function() use (...) { ... });

        $startTime = Carbon::createFromTimeString($start);
        $endTime   = Carbon::createFromTimeString($end);
        $slots = [];

        while ($startTime <= $endTime) {
            $slots[] = $startTime->format('h:i a'); // display format
            $startTime->addMinutes($intervalMinutes);
        }

        $mem[$key] = $slots;
        return $mem[$key];
    }

    /**
     * Generate booking slots for next future days (default 7).
     * Exclude past slots for today. If current time is not exactly on a slot
     * boundary, skip the immediate next slot as well (so user at 03:01 sees
     * 04:00 as first slot).
     *
     * @return array
     */
    public function generateBookingSlots(int $daysCount = 7): array
    {
        $days = [];
        $today = Carbon::today();

        $availableDaysConfig = json_decode($this->available_days,true) ?? [];

        // get this professional’s booked appointments in the next N days
        $appointments = Appointment::where('healthcare_professional_id', $this->id)
            ->whereBetween('appointment_start_time', [$today, $today->copy()->addDays($daysCount)])
            ->where('status', 'booked')
            ->get();

        for ($i = 0; $i <= $daysCount; $i++) {
            $date = $today->copy()->addDays($i);
            $dayName = strtoupper($date->format('D')); // MON, TUE...

            $dayConfig = collect($availableDaysConfig)->firstWhere('day', $dayName);
             // if no config or not available, no slots

            if (!$dayConfig || !$dayConfig['available']) {
                $days[] = [
                    'day'       => $dayName,
                    'date'      => $date->format('d'),
                    'full_date' => $date->toDateString(),
                    'available' =>$dayConfig['available'] ?? false,
                    'slots'     => [],
                ];
                continue;
            }

            $start = $dayConfig['work_start'];
            $end = $dayConfig['work_end'];
            $intervalMinutes = $dayConfig['slot_interval_minutes'];
            $baseSlots = $this->getBaseTimeSlots($start, $end, $intervalMinutes);

            $slotsForDay = [];
            foreach ($baseSlots as $slotStr) {
                $slotDateTime = Carbon::parse($date->toDateString() . ' ' . $slotStr);

                // check if this slot overlaps|booked with any appointment
                $isBooked = $appointments->contains(function ($appt) use ($slotDateTime, $intervalMinutes) {
                    $slotEnd = $slotDateTime->copy()->addMinutes($intervalMinutes);
                    return $slotDateTime < $appt->appointment_end_time &&
                        $slotEnd > $appt->appointment_start_time;
                });

                $slotsForDay[] = [
                    'time'      => $slotStr,
                    'available' => !$isBooked,
                ];
            }

            $days[] = [
                'day'       => $dayName,
                'date'      => $date->format('d'),
                'full_date' => $date->toDateString(),
                'available' =>$dayConfig['available'] ?? false,
                'slots'     => $slotsForDay,
            ];
        }

        return $days;
    }

}
