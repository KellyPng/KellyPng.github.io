<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReports extends Model
{
    use HasFactory;
    protected $table = 'reports';
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];
    
    protected $fillable = ['image', 'SUBJECT', 'description', 'email'];
}
