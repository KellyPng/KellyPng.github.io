<?php

namespace App\Models;

use App\Models\TicketType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketAvailability extends Model
{
    use HasFactory;
    protected $table = 'ticket_availabilities';
    protected $primarykey = 'id';
    protected $foreignkey = 'ticketTypeId';
    //protected $fillable = ['ticketTypeId', 'date', 'available_quantity'];
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticketTypeId');
    }

    public function parkTicket()
    {
        return $this->belongsTo(SingleParkTicket::class);
    }

    
}
