<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Hotel;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run()
    {
        $count = (int) $this->command->ask('Quantes notícies vols crear per a cada hotel existent (si n\'hi ha)?', 3);
        $hotels = Hotel::all();

        if ($hotels->isEmpty()) {
            $this->command->info('No hi ha hotels a la base de dades. Creant notícies sense associar.');
            News::factory()->count($count)->create(['published' => true]);
            $this->command->info("S'han creat {$count} notícies globals (no associades a cap hotel).");
            return;
        }

        $totalNewsCreated = 0;
        foreach ($hotels as $hotel) {
            $createdNews = News::factory()->count($count)->create(['published' => true]);
            foreach ($createdNews as $newsItem) {
                $newsItem->hotels()->attach($hotel->id);
            }
            $this->command->info("S'han creat {$count} notícies i associades a l'hotel '{$hotel->name}'.");
            $totalNewsCreated += $count;
        }
        
        $this->command->info("Total de {$totalNewsCreated} notícies creades i associades.");
    }
}