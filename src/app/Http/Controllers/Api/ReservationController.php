<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserve;
use App\Models\ReservationToken;
use App\Mail\ReservationVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Hotel;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reserves = Reserve::all();
        return response()->json($reserves);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $reserve = Reserve::create($request->all());
        return response()->json($reserve, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reserve = Reserve::find($id);
        return response()->json($reserve);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reserve = Reserve::find($id);
        $reserve->update($request->all());
        return response()->json($reserve);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reserve = Reserve::find($id);
        $reserve->delete();
        return response()->json(null, 204);
    }


    public function sendToken(Request $request)
    {
        $email = $request->input('email');
        // Generar un token único
        $token = Str::random(15);
        $expiresAt = Carbon::now()->addMinutes(30);

        // Guardar en la tabla
        ReservationToken::create([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Enviar email de verificación
        Mail::to($email)->send(new ReservationVerification($token));

        return response()->json(['success' => true]);
    }

    public function verifyToken(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');

        // Buscar el token activo y no expirado
        $reservationToken = ReservationToken::where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($reservationToken) {
            // Opcional: eliminar el token o marcarlo como usado
            return response()->json(['verified' => true]);
        } else {
            return response()->json(['verified' => false]);
        }
    }

    public function createReservation(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::channel('info_log')->info('Datos recibidos en createReservation:', $request->all());

            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'date_in' => 'required|date',
                'date_out' => 'required|date|after:date_in',
                'people' => 'required|integer|min:1',
                'type_room_id' => 'required|exists:type_rooms,id',
                'hotel_code' => 'required|exists:hotels,code',
                'rooms' => 'required|integer|min:1',
                'total_price' => 'required|numeric|min:0'
            ], [
                'hotel_code.required' => 'El código del hotel es requerido.',
                'hotel_code.exists' => 'El hotel especificado no existe.',
                'type_room_id.required' => 'El tipo de habitación es requerido.',
                'type_room_id.exists' => 'El tipo de habitación especificado no existe.'
            ]);

            if ($validator->fails()) {
                Log::channel('info_log')->error('Errores de validación:', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Obtener el hotel por su código
            $hotel = Hotel::where('code', $request->hotel_code)->first();
            if (!$hotel) {
                Log::channel('info_log')->error('Hotel no encontrado:', ['hotel_code' => $request->hotel_code]);
                return response()->json([
                    'errors' => ['hotel_code' => ['El hotel especificado no existe.']]
                ], 422);
            }

            // Verificar disponibilidad de habitaciones
            $availableRooms = Room::where('type_id', $request->type_room_id)
                ->where('hotel_id', $hotel->id) // Usar el ID del hotel obtenido
                ->whereDoesntHave('reserves', function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->whereBetween('date_in', [$request->date_in, $request->date_out])
                            ->orWhereBetween('date_out', [$request->date_in, $request->date_out])
                            ->orWhere(function ($q) use ($request) {
                                $q->where('date_in', '<=', $request->date_in)
                                    ->where('date_out', '>=', $request->date_out);
                            });
                    });
                })
                ->take($request->rooms)
                ->get();

            Log::channel('info_log')->info('Habitaciones disponibles encontradas:', [
                'count' => $availableRooms->count(),
                'rooms' => $availableRooms->pluck('id')
            ]);

            if ($availableRooms->count() < $request->rooms) {
                return response()->json([
                    'error' => 'No hay suficientes habitaciones disponibles para las fechas seleccionadas.'
                ], 422);
            }

            // Buscar o crear el usuario
            $role = Role::where('name', 'customer')->first();
            $userEmail = $request->email;
            $userName = explode('@', $userEmail)[0];
            
            $user = User::firstOrCreate(
                ['email' => $userEmail],
                [
                    'name' => $userName,
                    'role_id' => $role->id
                ]
            );

            // Crear las reservas para cada habitación
            $reservations = [];
            foreach ($availableRooms as $room) {
                $reservation = Reserve::create([
                    'room_id' => $room->id,
                    'user_id' => $user->id,
                    'people' => $request->people,
                    'date_in' => $request->date_in,
                    'date_out' => $request->date_out,
                    'price' => $request->total_price / $request->rooms,
                    'status' => 'pendent'
                ]);
                $reservations[] = $reservation;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada con éxito',
                'reservations' => $reservations
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('info_log')->error('Error al crear la reserva:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function calculatePrice($room, $people, $dateIn, $dateOut)
    {
        // Calcular la duración de la estancia en noches
        $dateIn = Carbon::parse($dateIn);
        $dateOut = Carbon::parse($dateOut);
        $nights = $dateOut->diffInDays($dateIn);

        // Precio base de la habitación
        $roomPrice = $room->price;

        // Si la habitación tiene servicios adicionales, sumarlos al precio
        $services = $room->services ?? collect(); // Asigna una colección vacía si es null
        $servicesTotal = $services->reduce(function ($acc, $service) {
            return $acc + $service->price;
        }, 0);

        // Precio por persona (si aplica)
        $pricePerPerson = $roomPrice * $nights * $people;

        // Sumar el precio base de la habitación y los servicios adicionales
        $totalPrice = $pricePerPerson + $servicesTotal;

        return $totalPrice;
    }
}
