<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'guest_name',
        'email',
        'check_in',
        'check_out',
        'guests',
        'room_type_id',
        'total_price',
        'feedback_token',
        'feedback_token_expires_at',
        'feedback_submitted_at'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'guests' => 'integer',
        'total_price' => 'decimal:2',
        'feedback_token_expires_at' => 'datetime',
        'feedback_submitted_at' => 'datetime'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
} 