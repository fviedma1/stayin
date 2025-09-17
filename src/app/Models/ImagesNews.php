<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImagesNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'news_id',
    ];

    public function news()
    {
        return $this->belongsToMany(News::class);
    }
}
