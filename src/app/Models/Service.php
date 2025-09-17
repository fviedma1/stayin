<?php

// app/Models/Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable =  ['name', 'description', 'price'];
    // Indica que un servicio pertenece a un tipo de habitación
    public function typeRooms()
    {
        return $this->belongsToMany(TypeRoom::class, 'type_room_service', 'service_id', 'type_room_id');
    }
}
