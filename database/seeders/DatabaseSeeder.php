<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tour;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@bymex.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        Tour::insert([
            ['title' => 'Visita a los Tuxtlas', 'destination' => 'Los Tuxtlas', 'departure_date' => '2026-07-23 20:00:00', 'price' => 6700.00, 'total_seats' => 49, 'expiration_hours' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Ruta de los Santuarios', 'destination' => 'Santuarios', 'departure_date' => '2026-04-30 18:00:00', 'price' => 9900.00, 'total_seats' => 49, 'expiration_hours' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Visita a la Inmaculada Virgen de Juquila', 'destination' => 'Juquila', 'departure_date' => '2026-05-22 22:00:00', 'price' => 3900.00, 'total_seats' => 49, 'expiration_hours' => 24, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
