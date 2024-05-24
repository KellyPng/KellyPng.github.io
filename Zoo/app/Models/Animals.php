<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animals extends Model
{
    use HasFactory;
    public $primaryKey = 'id';
    public $foreignKey = 'parkId';

    public function park(){
        return $this->belongsTo(Parks::class, 'parkId');
    }
}
