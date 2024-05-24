<?php

namespace App\Models;

use App\Models\Parks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookPark extends Model
{
    use HasFactory;
    protected $table = 'book_parks';
    public $primaryKey = 'id';

    public function park()
    {
        return $this->belongsTo(Parks::class, 'parkID');
    }

    public function booking()
    {
        return $this->belongsTo(Bookings::class,'bookingID');
    }
}
