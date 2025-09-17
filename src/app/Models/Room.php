<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable =  ['type_id','name', 'number', 'extra_bed', 'hotel_id', 'state'];

    // Relación con la tabla type_rooms (pertenece a un TypeRoom)
    public function typeRoom()
    {
        return $this->belongsTo(TypeRoom::class, 'type_id');
    }

    // Relación con la tabla hotels (pertenece a un Hotel)
    public function hotels()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    // Relación muchos a muchos con usuaris (reserves como tabla pivot)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Relación uno a muchos con reserves
    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }

}
