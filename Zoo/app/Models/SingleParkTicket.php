<?php

namespace App\Models;

use App\Models\Parks;
use Illuminate\Support\Carbon;
use App\Models\TicketAvailability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SingleParkTicket extends Model
{
    use HasFactory;
    protected $casts = [
        'validfrom' => 'date:Y-m-d',
        'validtill' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
        'created_at' => 'date:Y-m-d'
    ];
    public function park()
    {
        return $this->belongsTo(Parks::class, 'parkId');
    }
    public function availabilities()
    {
        return $this->hasMany(SingleParkTicketAvailability::class);
    }

    // protected static function booted()
    // {
    //     static::created(function ($park) {
    //         $park->generateAndSaveAvailability();
    //     });

    //     static::updated(function ($park) {
    //         // Check if capacity or valid dates have changed
    //         if ($park->isDirty('capacity') || $park->isDirty('valid_from') || $park->isDirty('valid_till')) {
    //             // Delete existing availabilities and recreate
    //             $park->availabilities()->delete();
    //             $park->generateAndSaveAvailability();
    //         }
    //     });

    //     static::deleting(function ($park) {
    //         // Delete associated ticket_availabilities when TicketType is deleted
    //         $park->availabilities()->delete();
    //     });
    // }

    // // Helper function to generate availability data
    // public function generateAndSaveAvailability()
    // {
    //     $startDate = $this->valid_from ? Carbon::parse($this->valid_from) : now();
    //     $endDate = $this->valid_till ? Carbon::parse($this->valid_till) : now()->addYear();

    //     // Create ticket availabilities
    //     $this->availabilities()->createMany(
    //         $this->generateAvailabilityData($startDate, $endDate, $this->capacity)
    //     );
    // }
}
