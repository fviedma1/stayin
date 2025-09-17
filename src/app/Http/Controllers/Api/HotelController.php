<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Support\Carbon;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotels = Hotel::all();
        return response()->json($hotels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $hotel = Hotel::create($request->all());
        return response()->json($hotel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hotel = Hotel::find($id);
        return response()->json($hotel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hotel = Hotel::find($id);
        $hotel->update($request->all());
        return response()->json($hotel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hotel = Hotel::find($id);
        $hotel->delete();
        return response()->json(null, 204);
    }

    /**
     * Display the specified resource by identifier.
     */

    public function getHotelByIdentifier($identifier)
    {
        // Buscar el hotel por el identificador
        $hotel = Hotel::where('code', $identifier)->first();

        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $response = [
            'hotel' => [
                'name' => $hotel->name,
                'address' => $hotel->address,
                'city' => $hotel->city,
                'country' => $hotel->country,
                'telephone' => $hotel->telephone,
            ],
        ];
        return response()->json($response);
    }

    public function getTypeRooms($identifier)
    {
        // Buscar el hotel por el identificador
        $hotel = Hotel::where('code', $identifier)->first();
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $availableRooms = Room::with(['typeRoom.services'])
            ->where('hotel_id', $hotel->id)
            ->get();

        $availableRoomTypes = $availableRooms->map(function ($room) {
            return [
                'name' => $room->typeRoom->name,
                'description' => $room->typeRoom->description,
                'images' => $room->typeRoom->images
            ];
        })->unique('name')->values();

        // Estructurar la respuesta con la información necesaria
        $response = [
            'room_types' => $availableRoomTypes
        ];

        return response()->json($response);
    }

    public function getTypeRoomsToSearch($identifier)
    {
        // Buscar el hotel por el identificador
        $hotel = Hotel::where('code', $identifier)->first();
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $availableRooms = Room::with(['typeRoom.services'])
            ->where('hotel_id', $hotel->id)
            ->get();

        $availableRoomTypes = $availableRooms->map(function ($room) {
            return [
                'name' => $room->typeRoom->name,
                'price' => $room->typeRoom->price,
                'services' => $room->typeRoom->services->map(function ($service) {
                    return [
                        'name' => $service->name,
                        'price' => $service->price,
                    ];
                }),
            ];
        })->unique('name')->values();

        // Estructurar la respuesta con la información necesaria
        $response = [
            'room_types' => $availableRoomTypes
        ];

        return response()->json($response);
    }
    public function getSearch($identifier, Request $request)
    {
        // 1. Añadir validación del nuevo parámetro
        $validated = $request->validate([
            'date_in' => 'required|date',
            'date_out' => 'required|date|after_or_equal:date_in',
            'people' => 'nullable|integer|min:1' 
        ]);

        $startDate = Carbon::parse($validated['date_in']);
        $endDate = Carbon::parse($validated['date_out']);
        $people = $validated['people'] ?? null;

        // 2. Buscar el hotel con sus relaciones necesarias
        $hotel = Hotel::where('code', $identifier)
            ->with(['rooms.typeRoom.services', 'rooms.reserves'])
            ->first();

        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $query = $hotel->rooms()->with(['typeRoom.services', 'reserves']);

        // 3. Aplicar filtro de capacidad solo si se envía people
        if ($people) {
            $query->whereHas('typeRoom', function ($q) use ($people) {
                $q->whereRaw('(rooms.extra_bed + type_rooms.bed) >= ?', [$people]);
            });
        }

        $filteredRooms = $query->get(); 

        // 4. Filtrar por disponibilidad en fechas
        $availableRooms = $filteredRooms->filter(function ($room) use ($startDate, $endDate) {
            $conflictingReservations = $room->reserves()
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_in', [$startDate, $endDate])
                        ->orWhereBetween('date_out', [$startDate, $endDate])
                        ->orWhere(function ($sub) use ($startDate, $endDate) {
                            $sub->where('date_in', '<', $startDate)
                                ->where('date_out', '>', $endDate);
                        });
                })->exists();

            return !$conflictingReservations;
        });

        // 5. Mapear resultados finales
        $response = [
            'room_types' => $availableRooms->mapToGroups(function ($room) {
                return [$room->typeRoom->id => [
                    'id' => $room->typeRoom->id,
                    'name' => $room->typeRoom->name,
                    'price' => $room->typeRoom->price,
                    'image' => $room->typeRoom->images,
                    'capacity' => $room->extra_bed + $room->typeRoom->bed,
                    // Servicios únicos por nombre y precio
                    'services' => $room->typeRoom->services
                        ->unique(function ($service) {
                            return $service->name.$service->price;
                        })
                        ->values()
                        ->map(function ($service) {
                            return [
                                'name' => $service->name,
                                'price' => $service->price
                            ];
                        })
                ]];
            })->map(function ($group) {
                return $group->first();
            })->values()
        ];        

        return response()->json($response);
    }
}
