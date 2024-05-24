<?php

namespace App\Models;

use App\Models\BookPark;
use App\Models\Visitors;
use App\Models\TicketType;
use App\Models\BookingDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bookings extends Model
{
    use HasFactory;
    public $primaryKey = 'id';

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitors::class, 'visitorID');
    }
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'bookID');
    }
    public function bookParks()
    {
        return $this->hasOne(BookPark::class, 'bookingID');
    }
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticketTypeID');
    }
}
