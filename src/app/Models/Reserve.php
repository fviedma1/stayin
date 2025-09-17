<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reserve extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'room_id', 
        'people', 
        'date_in', 
        'date_out', 
        'check_in', 
        'check_out', 
        'status', 
        'price',
        'feedback_token',
        'feedback_token_expires_at',
        'feedback_submitted_at'
    ];

    protected $casts = [
        'date_in' => 'date',
        'date_out' => 'date',
        'check_in' => 'date',
        'check_out' => 'date',
        'feedback_token_expires_at' => 'datetime',
        'feedback_submitted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function typeRoom()
    {
        return $this->room->typeRoom();
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'reserve_id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'reserve_id');
    }
}
