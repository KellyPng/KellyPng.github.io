<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleParkTicketAvailability extends Model
{
    use HasFactory;
    protected $table = 'single_park_ticket_availabilities';
    public function parkTicket()
    {
        return $this->belongsTo(SingleParkTicket::class,'parkTicketId');
    }
}
