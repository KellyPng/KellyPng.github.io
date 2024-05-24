<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Bookings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visitors extends Model
{
    use HasFactory;
    public function bookings()
    {
        return $this->hasMany(Bookings::class,'visitorID');
    }
    public function latestBookingDate()
    {
        $latestBooking = $this->bookings()->latest('created_at')->first();

        return $latestBooking ? $latestBooking->created_at->format('Y-m-d') : null;
    }
    public function visitorstatus()
    {
        $multipleBookings = $this->bookings()->count('visitorID') > 1;

        if (!$multipleBookings) {
            return 'New';
        } else {
            return 'Existing';
        }
    }
    public function visitDate()
    {
        $latestVisit = $this->bookings()->latest('bookingDate')->first();

        return $latestVisit ? Carbon::parse($latestVisit->bookingDate)->format('Y-m-d') : null;
    }
}
