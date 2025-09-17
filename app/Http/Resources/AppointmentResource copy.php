<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HealthcareProfessionalResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'about' => $this->about,
            'available' => $this->available,
            // 'degree' => $this->degree,
            'speciality' => $this->speciality,
        ];

        // include slots only if explicitly requested
        // if ($request->boolean('with_slots')) {
            
            $allDays = $this->generateBookingSlots();

            $data['days'] = collect($allDays)->map(fn($d) => [
                'day'       => $d['day'],
                'date'      => $d['date'],
                'full_date' => $d['full_date'],
            ])->values();
        // }

        if ($request->id) {
            // use trait logic; assume model uses HasBookingSlots or call service
            $data['slots_available'] = $this->generateBookingSlots();
        }

        return $data;
    }
}
