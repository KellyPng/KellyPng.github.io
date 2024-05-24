<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discounts extends Model
{
    use HasFactory;
    protected $fillable = [
        'discountFor',
        'title',
        'promo_code',
        'discount_percentage',
        'start_date',
        'end_date',
        'eligibility',
        'description',
    ];
    protected $dates = ['start_date', 'end_date'];
}
