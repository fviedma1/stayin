<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'short_description',
        'long_description',
        'published',
    ];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class);
    }

    public function images()
    {
        return $this->hasMany(ImagesNews::class);
    }
}
