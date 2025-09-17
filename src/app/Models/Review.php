<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['reserve_id', 'stars', 'comment', 'token', 'used'];

    public function reserve()
    {
        return $this->belongsTo(Reserve::class, 'reserve_id');
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class);
    }
}
