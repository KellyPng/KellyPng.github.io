<?php

namespace App\Models;

use App\Models\Parks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Events extends Model
{
    use HasFactory;
    protected $table = 'events';
    public $primaryKey = 'id';
    public $foreignKey = 'parkId';
    protected $casts = [
        'startDate' => 'date:Y-m-d',
        'endDate' => 'date:Y-m-d',
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d'
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function park()
    {
        return $this->belongsTo(Parks::class, 'parkId', 'id');
    }
}
