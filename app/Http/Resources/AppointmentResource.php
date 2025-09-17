<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id,
            'user_id'                     => $this->user_id,
            'healthcare_professional_id'  => $this->healthcare_professional_id,
            
            // Format start/end times nicely
            'appointment_start_time'      => Carbon::parse($this->appointment_start_time)->toDateTimeString(),
            'appointment_end_time'        => Carbon::parse($this->appointment_end_time)->toDateTimeString(),

            // Human-readable formats
            'appointment_date'            => Carbon::parse($this->appointment_start_time)->toDateString(),
            'appointment_day'             => strtoupper(Carbon::parse($this->appointment_start_time)->format('D')),
            'appointment_slot'            => Carbon::parse($this->appointment_start_time)->format('h:i a'). ' - ' . Carbon::parse($this->appointment_end_time)->format('h:i a'),

            'status'        => $this->status,
            'description'   => $this->description,

            // Relations (optional, only if loaded via with())
            // 'professional' => $this->whenLoaded('professional'),
            // 'user'         => $this->whenLoaded('user'),

            'created_at'   => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at'   => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
