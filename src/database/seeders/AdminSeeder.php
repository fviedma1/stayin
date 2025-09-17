<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuaris = [
            [
                'id' => 1,
                'name' => 'admin',
                'dni' => '12345678A',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'role_id' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($usuaris as $usuari) {
            User::updateOrCreate(
                [
                    'id' => $usuari['id'],
                    'name' => $usuari['name'],
                    'dni' => $usuari['dni'],
                    'email' => $usuari['email'],
                    'password' => $usuari['password'],
                    'role_id' => $usuari['role_id'],
                    'email_verified_at' => $usuari['email_verified_at'],
                    'created_at' => $usuari['created_at'],
                    'updated_at' => $usuari['updated_at'],
                ]
            );
        }
    }
}
