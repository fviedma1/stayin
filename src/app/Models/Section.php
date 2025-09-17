<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug'];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class)
            ->withPivot('is_visible', 'order', 'display_count');
    }
}
