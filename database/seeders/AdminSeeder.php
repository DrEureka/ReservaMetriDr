<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'Cambiar1234');
        $nombre   = env('ADMIN_NAME', 'Administrador');

        User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $nombre,
                'password' => $password,
                'role'     => User::ROL_ADMIN,
            ]
        );

        $this->command?->info("Admin creado: {$email}");
    }
}
