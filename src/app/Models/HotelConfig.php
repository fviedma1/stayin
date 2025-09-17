<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelConfig extends Model
{
    protected $fillable = [
        'hotel_id',
        'news_limit',
        'comments_limit',
        'sections_order',
        'sections_visibility'
    ];

    protected $casts = [
        'sections_order' => 'array',
        'sections_visibility' => 'array'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}