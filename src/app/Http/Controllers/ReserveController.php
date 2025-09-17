<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class ReserveController extends Controller
{
    public function updateStatus(Request $request, Reserve $reserve)
    {
        Log::channel('info_log')->info("Actualitzant estat de la reserva ID: {$reserve->id}", [
            'status_actual' => $reserve->status,
            'nou_status' => $request->input('status'),
        ]);

        $newStatus = $request->input('status');
        $reserve->status = $newStatus;

        if ($newStatus === 'checkin') {
            $reserve->check_in = now();
            Log::channel('info_log')->info('Check-in realitzat anmb éxit', ['reserve_id' => $reserve->id]);
        } elseif ($newStatus === 'checkout') {
            $reserve->check_out = now();
            Log::channel('info_log')->info('Check-out realitzat amb èxit', ['reserve_id' => $reserve->id]);
        }

        $reserve->save();

        $roomStatus = $request->input('state');
        $reserve->room->state = $roomStatus;
        $reserve->room->save();

        $message = $newStatus === 'check_in' ? 'check_in realizado con éxito' : 'check_out realizado con éxito';
        session()->flash('status', $message);

        return response()->json(['success' => true]);
    }
    public function create(Request $request, $hotelId, $roomId)
    {

        Log::channel('info_log')->info("Accedint a la vista de creació de reserva per l'hotel ID: {$hotelId}, habitació ID: {$roomId}");
        $hotel = Hotel::findOrFail($hotelId);
        $room = Room::findOrFail($roomId);
        $dateIn = $request->get('date_in', now()->toDateString());

        return view('recepcionist.create', compact('hotel', 'room', 'hotelId', 'dateIn'));
    }

    public function store(Request $request, $roomId)
    {
        // Obtener la habitación
        $room = Room::findOrFail($roomId);

        // Obtener el hotel asociado a la habitación
        $hotelId = $room->hotel_id;

        // Verificar si la habitación está bloqueada
        if ($room->state === 'bloquejada') {
            return back()->withErrors(['error' => "No es poden fer reserves en l'habitació {$room->number} ja que està en manteniment."]);
        }

        return $this->processReserveAndUser($request, $hotelId, $roomId);
    }
    /**
     * Procesar la creación del usuario y la reserva.
     */
    private function processReserveAndUser(Request $request, $hotelId, $roomId)
    {
        Log::channel('info_log')->info('Mètode store cridat', ['inputs' => $request->all()]);

        $validatedData = $this->validateReserveAndUser($request);

        try {
            $user = $this->createUser($validatedData);
            $this->createReserve($validatedData, $user->id, $hotelId, $roomId);

            return redirect()->route('recepcionist.gestor', ['hotelId' => $hotelId])
                ->with('success', 'Reserva i usuari creats correctament.');
        } catch (\Exception $e) {
            Log::error('Error creant la reserva o l\'usuari', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Hi ha hagut un problema en la creació de la reserva o l\'usuari.']);
        }
    }

    /**
     * Validar los datos de entrada para el usuario y la reserva.
     */
    private function validateReserveAndUser(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email|max:50',
            'dni' => 'required|string|unique:users,dni|max:9',
            'date_in' => 'nullable|date|after_or_equal:today',
            'date_out' => 'required|date|after_or_equal:date_in',
        ]);
    }

    /**
     * Crear un nuevo usuario.
     */
    private function createUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'dni' => $data['dni'],
            'password' => bcrypt('password123'), // Contraseña predeterminada
            'role_id' => Role::where('name', 'customer')->value('id'), // Rol de cliente
        ]);

        Log::channel('info_log')->info('Usuari creat amb èxit', ['user_id' => $user->id]);

        return $user;
    }

    /**
     * Crear una nueva reserva.
     */
    private function createReserve(array $data, $userId, $hotelId, $roomId)
    {
        $room = Room::findOrFail($roomId);
        $typeRoom = $room->typeRoom;
        $priceForDay = $typeRoom->price;

        $dateIn = Carbon::parse($data['date_in']);
        $dateOut = Carbon::parse($data['date_out']);

        $days = $dateIn->diffInDays($dateOut);
        $totalPrice = $priceForDay * $days;

        $reserva = Reserve::create([
            'room_id' => $roomId,
            'user_id' => $userId,
            'hotel_id' => $hotelId,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
            'price' => $totalPrice,
        ]);

        Log::channel('info_log')->info('Reserva creada amb èxit', [
            'room_id' => $reserva->room_id,
            'user_id' => $reserva->user_id,
            'price' => $reserva->price,
        ]);

        return $reserva;
    }
}
