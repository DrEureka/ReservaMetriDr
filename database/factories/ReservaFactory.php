<?php

namespace Database\Factories;

use App\Models\Reserva;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'fecha'             => CarbonImmutable::tomorrow()->format('Y-m-d'),
            'hora_inicio'       => '12:00',
            'hora_fin'          => '14:00',
            'ubicacion'         => 'A',
            'cantidad_personas' => 4,
            'estado'            => Reserva::ESTADO_CONFIRMADA,
        ];
    }

    public function cancelada(): static
    {
        return $this->state(fn () => [
            'estado'       => Reserva::ESTADO_CANCELADA,
            'cancelada_at' => now(),
        ]);
    }

    public function conMesas(int $cantidad, string $ubicacion = 'A', int $capacidad = 4): static
    {
        return $this->afterCreating(function (Reserva $reserva) use ($cantidad, $ubicacion, $capacidad) {
            $mesas = MesaFactory::new()
                ->count($cantidad)
                ->conCapacidad($capacidad)
                ->state(new Sequence(fn (Sequence $sequence) => ['numero' => $sequence->index + 1]))
                ->create(['ubicacion' => $ubicacion]);

            $reserva->mesas()->attach($mesas->pluck('id'));
        });
    }
}
