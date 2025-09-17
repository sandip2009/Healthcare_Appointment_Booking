<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasBookingSlots;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthcareProfessional extends Model
{
    use HasBookingSlots, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'about',
        'available',
        'speciality',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'available' => 'boolean',
        'available_days' => 'array',
    ];

    /**
     * Accessor for slots_available
     */
    public function getSlotsAvailableAttribute(): array
    {
        // Only show slots if professional is available
        if (!$this->available) {
            return [];
        }
        return $this->generateBookingSlots();
    }



}
