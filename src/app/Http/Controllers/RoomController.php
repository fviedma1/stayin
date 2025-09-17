<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\TypeRoom;

class RoomController extends Controller
{
    public function showOne($hotelId, $id)
    {
        // Obtenemos el hotel
        $hotel = Hotel::findOrFail($hotelId);
    
        // Buscamos la habitación dentro del hotel
        $room = $hotel->rooms()
            ->with('reserves')
            ->where('id', $id)
            ->firstOrFail();
    
        // Obtenemos la reserva pendiente para esta habitación
        $currentReserve = $room->reserves()->where('status', 'pendent')->first();
        
        // Obtenemos las fechas de la reserva pendiente
        $dateIn = $currentReserve ? $currentReserve->date_in : null;
        $dateOut = $currentReserve ? $currentReserve->date_out : null;
    
        // Retornamos la vista con la información del hotel, habitación, reserva pendiente y fechas
        return view('rooms.showOne', compact('hotel', 'room', 'currentReserve', 'dateIn', 'dateOut'));
    }

    public function show(Request $request, $hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);

        // Consulta básica de habitaciones del hotel
        $query = $hotel->rooms()->with(['typeRoom', 'reserves']);

        // Filtros
        if ($request->has('room_number') && $request->room_number) {
            $query->where('number', $request->room_number);
        }

        if ($request->has('room_type') && $request->room_type) {
            $query->where('type_id', $request->room_type); // Comparación por ID
        }

        if ($request->has('room_state') && $request->room_state) {
            $query->where('state', $request->room_state);
        }

        // Obtener habitaciones filtradas
        $rooms = $query->get();

        // Obtener todos los tipos de habitación
        $roomTypes = TypeRoom::all();

        return view('rooms.show', compact('rooms', 'hotelId', 'roomTypes'));
    }

}
