<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackReview extends Model
{
    use HasFactory;
    protected $table = 'feedbackandreviews';
    protected $fillable = ['visitor_name', 'description', 'stars', 'is_visible'];
}
