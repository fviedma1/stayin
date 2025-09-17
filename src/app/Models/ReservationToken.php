<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationToken extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'token', 'expires_at'];

    public $timestamps = true;

    public static function generate($email)
    {
        return self::create([
            'email' => $email,
            'token' => bin2hex(random_bytes(16)), // Genera un token aleatorio
            'expires_at' => now()->addMinutes(30) // Expira en 30 minutos
        ]);
    }
}
