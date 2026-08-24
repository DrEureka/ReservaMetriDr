<?php

namespace Database\Factories;

use App\Models\Mesa;
use Illuminate\Database\Eloquent\Factories\Factory;

class MesaFactory extends Factory
{
    protected $model = Mesa::class;

    public function definition(): array
    {
        static $secuencia = 0;

        return [
            'ubicacion' => 'A',
            'numero'    => ++$secuencia,
            'capacidad' => 4,
        ];
    }

    public function en(string $ubicacion): static
    {
        return $this->state(fn () => ['ubicacion' => $ubicacion]);
    }

    public function conCapacidad(int $capacidad): static
    {
        return $this->state(fn () => ['capacidad' => $capacidad]);
    }
}
