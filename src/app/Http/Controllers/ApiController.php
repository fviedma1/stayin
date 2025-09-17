<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Room;
use App\Models\Reserve;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\CheckoutMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class ApiController extends Controller
{
    public function getRooms(Request $request, $hotelId)
    {
        $startDateInput = $request->input('startDate');
        $currentDate = new DateTime($startDateInput ?: 'now');

        $days = [];
        $startDate = (clone $currentDate)->modify('-5 days');
        for ($i = 0; $i < 31; $i++) {
            $days[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }

        $startDateRange = $days[0];
        $endDateRange = end($days);

        $rooms = Room::where('hotel_id', $hotelId)
            ->with(['reserves' => function ($query) use ($startDateRange, $endDateRange) {
                $query->whereBetween('date_in', [$startDateRange, $endDateRange])
                    ->orWhereBetween('date_out', [$startDateRange, $endDateRange]);
            }, 'reserves.user', 'typeRoom'])
            ->get();

        return response()->json([
            'rooms' => $rooms,
            'days' => $days,
        ]);
    }

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
            Log::channel('info_log')->info('Check-in realitzat amb èxit', ['reserve_id' => $reserve->id]);
        } elseif ($newStatus === 'checkout') {
            $reserve->check_out = now();
            Log::channel('info_log')->info('Check-out realitzat amb èxit', ['reserve_id' => $reserve->id]);

            $reserve->save();

            // Enviar email solo en checkout
            if ($newStatus === 'checkout') {
                // Generar un token único
                $token = Str::random(64);

                // Guardar el review en la base de datos
                $review = new Review();
                $review->reserve_id = $reserve->id;
                $review->token = $token;
                $review->save();
            }

            try {
                Mail::to($reserve->user->email)
                    ->send(new CheckoutMail($reserve, $token));

                Log::channel('info_log')->info('Email de checkout enviado', [
                    'reserve_id' => $reserve->id,
                    'user_email' => $reserve->user->email
                ]);
            } catch (\Exception $e) {
                Log::channel('error_log')->error('Error enviando email de checkout', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $roomStatus = $request->input('state');
        $reserve->room->state = $roomStatus;
        $reserve->room->save();

        $message = $newStatus === 'check_in' ? 'check_in realizado con éxito' : 'check_out realizado con éxito';
        session()->flash('status', $message);

        return response()->json(['success' => true]);
    }

    public function updateRoomStatus(Request $request, $roomNumber)
    {
        try {
            // Buscar la habitación por su número
            $room = Room::where('number', $roomNumber)->firstOrFail();

            // Obtener el estado deseado del request
            $desiredState = $request->input('bloquejada', 'bloquejada');

            // Verificar el estado actual y cambiarlo según corresponda
            if ($room->state == $desiredState) {
                $room->state = 'lliure';
            } else {
                $room->state = 'bloquejada';
            }

            $room->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado de la habitación actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de la habitación: ' . $e->getMessage()
            ], 500);
        }
    }
}
