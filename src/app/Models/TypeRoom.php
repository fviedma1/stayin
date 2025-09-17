<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'bed', 'images'];

    // Aixo fica dintre de rooms el id_type
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Relación muchos a muchos con servicios (tabla pivot type_room_service)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'type_room_service', 'type_room_id', 'service_id');
    }
}
