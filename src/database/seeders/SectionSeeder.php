<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['name' => 'Noticies', 'slug' => 'news'],
            ['name' => 'Tipus d\'habitacions', 'slug' => 'room_types'],
            ['name' => 'Resenyes', 'slug' => 'reviews']
        ];

        foreach ($sections as $section) {
            Section::updateOrCreate(
                ['slug' => $section['slug']],
                $section
            );
        }
    }
}
