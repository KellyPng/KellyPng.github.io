<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;
    public $primaryKey = 'id';
    
    public function ticketType(){
        return $this->belongsTo(TicketType::class,'ticketTypeId');
    }

    public function category(){
        return $this->belongsTo(DemoCategory::class,'demoCategoryId');
    }
}
