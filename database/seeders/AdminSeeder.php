<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@dramadan.com'],
            [
                'name'     => 'Administrador',
                'password' => 'Cambiar1234',
                'role'     => User::ROL_ADMIN,
            ]
        );

        $this->command?->info('Admin creado: admin@dramadan.com / Cambiar1234');
    }
}
