<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Carbon;
use App\Models\Reserve;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $room = Room::create($request->all());
        return response()->json($room, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::find($id);
        return response()->json($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::find($id);
        $room->update($request->all());
        return response()->json($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::find($id);
        $room->delete();
        return response()->json(null, 204);
    }

    /**
     * Display all reletacions of the specified resource.
     */
    public function getRoomsTypeRoomsServices(string $id)
    {
        $room = Room::with(['typeRoom', 'services'])->find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room);
    }

    public function getRoomsWithFilters(Request $request)
    {
        // Validar los parámetros de la solicitud
        $request->validate([
            'location' => 'nullable|string',
            'people' => 'nullable|integer|min:1',
            'date_in' => 'nullable|date',
            'date_out' => 'nullable|date|after_or_equal:date_in',
        ]);

        // Obtener los parámetros de la solicitud
        $location = $request->input('location');
        $people = $request->input('people');
        $dateIn = $request->input('date_in');
        $dateOut = $request->input('date_out');

        // Iniciar la consulta base
        $query = Room::with(['typeRoom', 'hotels', 'services', 'reserves']);

        // Aplicar filtro por localidad (si se proporciona)
        if ($location) {
            $query->whereHas('hotels', function ($q) use ($location) {
                $q->where('city', 'like', "%$location%")
                    ->orWhere('country', 'like', "%$location%");
            });
        }

        // Aplicar filtro por capacidad (si se proporciona)
        if ($people) {
            $query->whereHas('typeRoom', function ($q) use ($people) {
                $q->whereRaw('bed + IF(extra_bed = 1, 1, 0) >= ?', [$people]);
            });
        }

        // Aplicar filtro por rango de fechas sin reservas superpuestas
        if ($dateIn && $dateOut) {
            $query->whereDoesntHave('reserves', function ($q) use ($dateIn, $dateOut) {
                $q->where(function ($query) use ($dateIn, $dateOut) {
                    // Asegurarse de que no haya reservas que se solapen
                    $query->where(function($query) use ($dateIn, $dateOut) {
                        $query->where('date_in', '<', $dateOut) // La reserva empieza antes de la fecha de salida
                              ->where('date_out', '>', $dateIn); // La reserva termina después de la fecha de entrada
                    });
                });
            });
        }

        // Obtener las habitaciones filtradas
        $rooms = $query->get();

        $groupedRooms = $rooms->groupBy(['hotel_id', 'type_id'])->map(function ($hotelGroup) {
            return $hotelGroup->map(function ($typeGroup) {
                return $typeGroup->first(); // Selecciona la primera habitación de cada grupo
            });
        });

        // Aplanar la estructura para devolver una colección simple
        $uniqueRooms = $groupedRooms->flatten(1);

        // Devolver las habitaciones únicas
        return response()->json($uniqueRooms);
    }
}
