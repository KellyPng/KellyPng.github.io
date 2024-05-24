<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Models\TicketAvailability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketType extends Model
{
    use HasFactory;
    public $primaryKey = 'id';
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d'
    ];

    public function pricings()
    {
        return $this->hasMany(Pricing::class,'ticketTypeId');
    }
    public function availabilities()
    {
        return $this->hasMany(TicketAvailability::class,'id');
    }

}
