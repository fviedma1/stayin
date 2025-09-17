<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'rating',
        'comment',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
        'rating' => 'integer'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}