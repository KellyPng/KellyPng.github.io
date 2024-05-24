<?php

namespace App\Models;

use App\Models\Animals;
use App\Models\BookPark;
use App\Models\SingleParkTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parks extends Model
{
    use HasFactory;
    protected $table = 'parks';
    public $primaryKey = 'id';
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d'
    ];

    public function animals(){
        return $this->hasMany(Animals::class, 'parkId');
    }

    public function singleParkTicket()
{
    return $this->hasOne(SingleParkTicket::class, 'parkId');
}
public function bookParks()
{
    return $this->hasMany(BookPark::class, 'parkID');
}
}
