<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'city', 'country', 'image', 'telephone', 'email', 'code', 'receptionist_id'];

    // Relación uno a muchos con la tabla rooms
    public function recepcionist()
    {
        return $this->belongsTo(User::class, 'receptionist_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Relación hasManyThrough con la tabla reserves a través de rooms
    public function reserves()
    {
        return $this->hasManyThrough(Reserve::class, Room::class);
    }

    public function news()
    {
        return $this->belongsToMany(News::class);
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class)->withPivot('is_visible', 'order', 'display_count')
            ->orderBy('order');;
    }

    public function countRoomsByStatus($state)
    {
        return $this->rooms()->where('state', $state)->count();
    }

    public function countRoomByLliure()
    {
        return $this->countRoomsByStatus('lliure');
    }

    public function countRoomsByOcupada()
    {
        return $this->countRoomsByStatus('ocupada');
    }

    public function countRoomsByReservada()
    {
        return $this->countRoomsByStatus('reservada');
    }

    public function storeHotel($dades)
    {
        Hotel::create($dades);
    }

    public static function createHotelWithReceptionist(array $hotelData, int $roleSecretaryId): Hotel
    {
        try {
            // Verificar si el rol de secretario existe
            $roleSecretar = Role::find($roleSecretaryId);
            if (!$roleSecretar) {
                throw new \Exception("El rol 'secretary' no existe.");
            }

            // Validar si los datos necesarios están presentes
            if (empty($hotelData['receptionist_name']) || empty($hotelData['receptionist_password'])) {
                throw new \Exception("Faltan datos para el recepcionista.");
            }

            // Crear el usuario recepcionista con los datos proporcionados
            $receptionist = User::create([
                'name' => $hotelData['receptionist_name'],
                'dni' => uniqid('dni_'), // Usar un valor único para evitar colisiones
                'password' => Hash::make($hotelData['receptionist_password']),
                'role_id' => $roleSecretaryId,
            ]);

            // Crear el hotel y asociarlo al recepcionista
            $hotel = self::create(array_merge($hotelData, [
                'receptionist_id' => $receptionist->id,
            ]));
            return $hotel;
        } catch (\Exception $e) {
            // Manejo de errores: registrar el error y lanzar una excepción
            Log::error('Error al crear el hotel con recepcionista', [
                'error' => $e->getMessage(),
                'hotel_data' => $hotelData,
            ]);

            throw $e;  // Re-lanzar la excepción para que se maneje en el controlador
        }
    }
}
