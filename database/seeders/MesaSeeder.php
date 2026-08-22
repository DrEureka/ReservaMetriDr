<?php

namespace Database\Seeders;

use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $mesas = [
            // Sección A — 6 mesas
            ['ubicacion' => 'A', 'numero' => 1, 'capacidad' => 2],
            ['ubicacion' => 'A', 'numero' => 2, 'capacidad' => 2],
            ['ubicacion' => 'A', 'numero' => 3, 'capacidad' => 4],
            ['ubicacion' => 'A', 'numero' => 4, 'capacidad' => 4],
            ['ubicacion' => 'A', 'numero' => 5, 'capacidad' => 6],
            ['ubicacion' => 'A', 'numero' => 6, 'capacidad' => 8],

            // Sección B — 5 mesas
            ['ubicacion' => 'B', 'numero' => 1, 'capacidad' => 2],
            ['ubicacion' => 'B', 'numero' => 2, 'capacidad' => 2],
            ['ubicacion' => 'B', 'numero' => 3, 'capacidad' => 4],
            ['ubicacion' => 'B', 'numero' => 4, 'capacidad' => 4],
            ['ubicacion' => 'B', 'numero' => 5, 'capacidad' => 6],

            // Sección C — 4 mesas
            ['ubicacion' => 'C', 'numero' => 1, 'capacidad' => 2],
            ['ubicacion' => 'C', 'numero' => 2, 'capacidad' => 4],
            ['ubicacion' => 'C', 'numero' => 3, 'capacidad' => 4],
            ['ubicacion' => 'C', 'numero' => 4, 'capacidad' => 6],

            // Sección D — 4 mesas
            ['ubicacion' => 'D', 'numero' => 1, 'capacidad' => 2],
            ['ubicacion' => 'D', 'numero' => 2, 'capacidad' => 2],
            ['ubicacion' => 'D', 'numero' => 3, 'capacidad' => 4],
            ['ubicacion' => 'D', 'numero' => 4, 'capacidad' => 6],
        ];

        foreach ($mesas as $mesa) {
            Mesa::updateOrCreate(
                ['ubicacion' => $mesa['ubicacion'], 'numero' => $mesa['numero']],
                ['capacidad' => $mesa['capacidad']]
            );
        }
    }
}
