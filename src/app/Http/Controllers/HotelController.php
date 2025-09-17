<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    public function index()
    {
        return view('hotel.index', ['hotels' => Hotel::all()]);
    }

    public function showOne(Request $request, $id)
    {
        try {
            // Obtener la lista de todos los hoteles
            $allHotels = Hotel::all();

            // Si hay un parámetro hotel_id, cargar ese hotel
            if ($request->has('hotel_id') && $request->hotel_id != '') {
                $hotel = Hotel::findOrFail($request->hotel_id);
            } else {
                // Si no hay hotel_id, usar el hotel que se pasa como parámetro en la URL
                $hotel = Hotel::findOrFail($id);
            }

            // Datos sobre las habitaciones
            $totalRooms = $hotel->rooms()->count(); // Habitaciones totales
            $roomsLliures = $hotel->countRoomByLliure(); // Habitaciones libres
            $roomsOcupades = $hotel->countRoomsByOcupada(); // Habitaciones ocupadas
            $roomsReservades = $hotel->countRoomsByReservada(); // Habitaciones reservadas

            return view('hotel.showOne', [
                'hotel' => $hotel,
                'allHotels' => $allHotels,
                'totalRooms' => $totalRooms,
                'roomsLliures' => $roomsLliures,
                'roomsOcupades' => $roomsOcupades,
                'roomsReservades' => $roomsReservades
            ]);
        } catch (\Exception $e) {
            Log::error('Error inesperado al obtener hotel en showOne', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ha ocurrido un problema en la búsqueda del hotel.']);
        }
    }

    public function create()
    {
        return view('hotel.create');
    }

    public function description($id)
    {
        Log::channel('info_log')->info('Método description llamado en', ['id' => $id]);
        $hotel = Hotel::findOrFail($id);
        return view('hotel.description', compact('hotel'));
    }
    public function store(Request $request)
    {
        // Validar el rol de secretario
        $role_secretary = Role::where('name', 'secretary')->value('id');
        if (!$role_secretary) {
            Log::error('El rol "secretary" no existe en la base de datos.');
            return back()->withErrors(['error' => 'El rol "secretary" no se encuentra en la base de datos.']);
        }

        // Validación por pasos
        $validatedData = $request->validate([
            // Paso 1: Información básica
            'name' => 'required|string|max:50',
            'address' => 'required|string|max:150',
            'code' => 'required|string|max:25',

            // Paso 2: Ubicación
            'country' => 'required|string|max:25',
            'city' => 'required|string|max:25',

            // Paso 3: Contacto
            'telephone' => 'required|max:15',
            'email' => 'required|email',

            // Paso 4: Configuración
            'num_rooms' => 'required|integer|min:1|max:100',
            'num_users' => 'required|integer|min:1|max:100',
            'num_reserves' => 'required|integer|min:1|max:200',
            'num_reviews' => 'required|integer|min:1|max:200',
            'num_news' => 'required|integer|min:1|max:200',

            // Paso 5: Recepcionista
            'receptionist_name' => 'required|string|max:50|unique:users,name',
            'receptionist_password' => 'required|string|',
        ]);

        try {
            DB::beginTransaction();
            // Crear el hotel y el recepcionista con los datos ingresados
            $hotel = Hotel::createHotelWithReceptionist($validatedData, $role_secretary);

            // Generar datos adicionales (habitaciones, usuarios, reservas)
            $seeder = new DatabaseSeeder();
            $seeder->initializeHotelSeeders(
                $hotel,
                $validatedData['num_users'],
                $validatedData['num_rooms'],
                $validatedData['num_reserves'],
                $validatedData['num_reviews'],
                $validatedData['num_news']
            );

            DB::commit();
            // Redirigir con mensaje de éxito
            return redirect()->route('hotel.index')->with('success', 'Hotel creat exitosament!!');
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir cambios en caso de error

            Log::error('Error en la creación del hotel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Error completo en la creación: ' . $e->getMessage()]);
        }
    }
}