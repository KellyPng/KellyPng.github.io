<?php

namespace App\Models;

use App\Models\Pricing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemoCategory extends Model
{
    use HasFactory;
    protected $table = 'demo_categories';
    public $primaryKey = 'id';

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }
}
