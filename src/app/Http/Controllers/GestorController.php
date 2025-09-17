<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Room;
use App\Models\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GestorController extends Controller
{
    public function index(Request $request, $hotelId)
    {
        Log::channel('info_log')->info("S'està accedint a la vista de gestor per l'hotel ID: {$hotelId}");
        // Obtener habitaciones del hotel con reservas y usuarios asociados
        $rooms = Room::where('hotel_id', $hotelId)->with(['reserves.user'])->get();

        Log::channel('info_log')->info('S\'han carregat les habitacions del gestor amb èxit.', [
            'hotelId' => $hotelId,
            'totalRooms' => $rooms->count(),
        ]);

        return view('recepcionist.gestor', compact('rooms', 'hotelId'));
    }

    public function reserves(Request $request, $hotelId)
    {
        Log::channel('info_log')->info("S'està accedint a la vista de reserves per l'hotel ID: {$hotelId}");

        // Validar las entradas del formulario
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'client_name' => 'nullable|string|max:255',
            'reserve_id' => 'nullable|integer',
        ], [
            'end_date.after_or_equal' => 'La data final no pot ser anterior a la data d\'inici.',
            'reserve_id.integer' => 'L\'ID de reserva ha de ser un número vàlid.',
            'client_name.string' => 'El nom del client ha de ser un text vàlid.',
        ]);

        $startDate = $validatedData['start_date'] ?? null;
        $endDate = $validatedData['end_date'] ?? null;
        $clientName = $validatedData['client_name'] ?? null;
        $reserveId = $validatedData['reserve_id'] ?? null;

        Log::channel('info_log')->info('S\'han rebut els següents filtres per les reserves.', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'clientName' => $clientName,
            'reserveId' => $reserveId,
        ]);

        $query = Reserve::query();

        // Filtrar reservas por el hotel a través de las habitaciones
        $query->whereHas('room', function ($roomQuery) use ($hotelId) {
            $roomQuery->where('hotel_id', $hotelId);
        });

        // Filtro por fecha de inicio
        if ($startDate) {
            $query->where('date_in', '>=', $startDate);
        }

        // Filtro por fecha de finalización
        if ($endDate) {
            $query->where('date_out', '<=', $endDate);
        }

        // Filtro por nombre del cliente
        if ($clientName) {
            $query->whereHas('user', function ($userQuery) use ($clientName) {
                $userQuery->where('name', 'like', '%' . $clientName . '%');
            });
        }

        // Filtro por ID de reserva
        if ($reserveId) {
            $query->where('id', $reserveId);
        }

        // Filtrar solo reservas con estado "checkin"
        $reserves = $query->where('status', 'pendent')->get();

        $currentDate = now()->format('Y-m-d');

        return view('recepcionist.reserves', [
            'reserves' => $reserves,
            'currentDate' => $currentDate,
            'hotelId' => $hotelId,
        ]);
    }
}
