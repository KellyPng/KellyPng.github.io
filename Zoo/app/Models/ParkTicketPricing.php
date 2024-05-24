<?php

namespace App\Models;

use App\Models\DemoCategory;
use App\Models\SingleParkTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParkTicketPricing extends Model
{
    use HasFactory;
    public function singleParkTicket(){
        return $this->belongsTo(SingleParkTicket::class,'parkTicketId');
    }
    public function category(){
        return $this->belongsTo(DemoCategory::class,'demoCategoryId');
    }
}
