<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Review;
use App\Models\TypeRoom;
use App\Models\User;
use App\Models\Reserve;
use App\Models\Role;
use App\Models\News;
use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        if ($this->command->confirm('Vols refrescar la base de dades?', true)) {
            $this->command->call('migrate:fresh');
            $this->command->info("S'ha reconstruït la base de dades");
        }

        $this->command->info('Base de dades inicialitzada amb exit.');

        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
        ]);
    }

    public function initializeHotelSeeders(Hotel $hotel, $numUsers, $numRooms, $numReserves, $numReviews, $numNews)
    {
        DB::beginTransaction(); // Iniciar transacción

        try {
            $this->seedServices();
            $this->seedTypeRooms();
            $this->seedSections();
            $this->seedUsers($numUsers);
            $this->seedRooms($hotel, $numRooms);
            $this->seedReservations($hotel, $numReserves);
            $this->seedReviews($hotel, $numReviews);
            $this->seedNews($hotel, $numNews);
            $this->seedHotelSections($hotel);

            DB::commit(); // Confirmar cambios si todo OK
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir todos los cambios en caso de error
            throw $e; // Relanzar la excepción para capturarla en el controlador
        }
    }
    private function seedServices()
    {
        $this->call([ServeisSeeder::class]);
    }
    private function seedTypeRooms()
    {
        $this->call([TypeRoomSeeder::class]);
    }

    private function seedSections()
    {
        $this->call([SectionSeeder::class]);
    }

    private function seedHotelSections(Hotel $hotel)
    {
        $sections = Section::all();

        $order = 1;
        foreach ($sections as $section) {
            $hotel->sections()->attach($section->id, [
                'is_visible' => true,
                'order' => $order++,
            ]);
        }
    }

    private function seedUsers($numUsers)
    {

        $role_customer = Role::where('name', 'customer')->value('id');
        User::factory()->count($numUsers)->create(['role_id' => $role_customer]);
    }


    private function seedRooms(Hotel $hotel, $numRooms)
    {
        $typeRooms = TypeRoom::all();

        foreach (range(1, $numRooms) as $i) {
            $this->createRoom($hotel, $typeRooms->random());
        }
    }
    private function createRoom(Hotel $hotel, TypeRoom $typeRoom)
    {

        Room::factory()->create([
            'hotel_id' => $hotel->id,
            'type_id' => $typeRoom->id,
        ]);
    }

    private function seedReservations(Hotel $hotel, $numReserves)
    {
        $role_customer = Role::where('name', 'customer')->value('id');

        $users = User::where('role_id', $role_customer)->get();
        $rooms = $hotel->rooms;

        if ($users->isEmpty() || $rooms->isEmpty()) {
            return;
        }

        foreach (range(1, $numReserves) as $i) {
            $this->createReservation($rooms->random(), $users->random());
        }
    }

    private function createReservation(Room $room, User $user)
    {
        $today = new \DateTime();

        // Variables per a les dates de reserva
        $minDaysToCheckIn = 0;
        $maxDaysToCheckIn = 7;
        $minStayDuration = 1;
        $maxStayDuration = 5;

        // Generar data d'entrada entre avui i +7 dies
        $dateIn = (clone $today)->modify('+' . random_int($minDaysToCheckIn, $maxDaysToCheckIn) . ' days')->format('Y-m-d');

        // Generar data de sortida entre +1 i +5 dies després de `dateIn`
        $dateOut = (new \DateTime($dateIn))->modify('+' . random_int($minStayDuration, $maxStayDuration) . ' days')->format('Y-m-d');

        // Crear la reserva amb dates vàlides
        Reserve::factory()->create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
        ]);
    }

    private function seedReviews(Hotel $hotel, $numReviews)
    {
        $reserves = Reserve::all();

        // Asegurarse de que no se creen más feedbacks que reservas
        $numReviews = min($numReviews, $reserves->count());

        // Seleccionar reservas aleatorias para crear feedbacks
        $reserves->random($numReviews)->each(function ($reserve) {
            $this->createReview($reserve);
        });
    }

    private function createReview(Reserve $reserve)
    {
        Review::factory()->create([
            'reserve_id' => $reserve->id,
        ]);
    }

    private function seedNews(Hotel $hotel, $numNews)
    {
        $this->createNewsForHotel($hotel, $numNews);
    }

    private function createNewsForHotel(Hotel $hotel, $numNews)
    {
        Log::info("[DBSeeder] Iniciando createNewsForHotel para Hotel ID: {$hotel->id}, Nombre: {$hotel->name}, Número de Noticias: {$numNews}");
        
        if ($numNews == 0) {
            Log::info("[DBSeeder] numNews es 0, no se crearán noticias para el hotel {$hotel->id}.");
            return;
        }

        for ($i = 0; $i < $numNews; $i++) {
            Log::info("[DBSeeder] Iteración {$i} para crear noticia para Hotel ID: {$hotel->id}");
            try {
                Log::info("[DBSeeder] Intentando News::factory()->create() para Hotel ID: {$hotel->id}");
                // Crear una nueva noticia
                $news = News::factory()->create(); // Utiliza la factoría NewsFactory
                Log::info("[DBSeeder] Noticia ID: {$news->id} creada (Título: {$news->title}, ShortDesc: {$news->short_description}). Intentando asociar a Hotel ID: {$hotel->id}");
                
                // Asociar esta noticia específicamente al hotel proporcionado
                $hotel->news()->attach($news->id);
                
                Log::info("[DBSeeder] Noticia ID: {$news->id} asociada exitosamente al Hotel ID: {$hotel->id}");
            } catch (\Illuminate\Database\QueryException $qe) {
                Log::error("[DBSeeder] QueryException al crear/asociar noticia para Hotel ID: {$hotel->id}. Iteración {$i}. Mensaje: " . $qe->getMessage() . " SQL: " . $qe->getSql() . " Bindings: " . implode(", ", $qe->getBindings()));
            } catch (\Exception $e) {
                Log::error("[DBSeeder] Exception general al crear/asociar noticia para Hotel ID: {$hotel->id}. Iteración {$i}. Mensaje: " . $e->getMessage());
            }
        }
        Log::info("[DBSeeder] Finalizado createNewsForHotel para Hotel ID: {$hotel->id}");
    }
}