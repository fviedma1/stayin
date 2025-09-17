<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin'],
            ['name' => 'secretary'],
            ['name' => 'customer'],
        ];

        foreach ($roles as $rol) {
            Role::updateOrCreate(
                ['name' => $rol['name']]
            );
        }

        $this->command->info('Rols inicialitzats amb èxit.');
    }
}
