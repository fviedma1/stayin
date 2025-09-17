<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserve;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Role;



class ReserveController extends Controller
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

    public function storeReserve(Request $request)
    {
        Log::channel('info_log')->info('Datos recibidos en la API:', $request->all());

        // Validar los datos de la reserva
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'room_id' => 'required|exists:rooms,id',
            'date_in' => 'required|date',
            'date_out' => 'required|date|after:date_in',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::channel('info_log')->error('Error de validación:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::where('name', 'customer')->first();
            $userEmail = $request->email;

            // Generar el nombre a partir del email (eliminando todo después del @)
            $userName = explode('@', $userEmail)[0];

            // Buscar o crear el usuario con el nombre generado
            $user = User::firstOrCreate(
                ['email' => $userEmail],
                [
                    'name' => $userName,
                    'role_id' => $role->id
                ]
            );

            Log::channel('info_log')->info('Usuario encontrado o creado:', ['user' => $user]);

            // Crear la reserva
            $reservation = Reserve::create([
                'room_id' => $request->room_id,
                'user_id' => $user->id,
                'date_in' => $request->date_in,
                'date_out' => $request->date_out,
                'price' => $request->price,
                'status' => 'pendent', // Estado por defecto
            ]);

            Log::channel('info_log')->info('Reserva creada:', ['reservation' => $reservation]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reserva creada con éxito',
                'reservation' => $reservation
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('info_log')->error('Error al crear la reserva:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al crear la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
