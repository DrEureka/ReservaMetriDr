<?php

namespace App\Services;

use App\Models\Reserva;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class AsignacionUbicacionService
{
    public function __construct(
        private DisponibilidadService $disponibilidad,
        private HorarioService $horarios,
    ) {}

    public function asignar(CarbonImmutable $fecha, string $horaInicio, int $personas, bool $desdeCache = true): ?array
    {
        foreach (Reserva::UBICACIONES as $ubicacion) {
            $libres = collect($this->disponibilidad->mesasLibres($ubicacion, $fecha, $horaInicio, $desdeCache));

            if ($libres->isEmpty()) {
                continue;
            }

            $corrida = $this->buscarCorridaOptima($libres, $personas, $this->horarios->maxMesasPorReserva());
            if ($corrida !== null) {
                return [
                    'ubicacion' => $ubicacion,
                    'mesas'     => $corrida,
                ];
            }
        }

        return null;
    }

    private function buscarCorridaOptima(Collection $libres, int $personas, int $maxMesas): ?Collection
    {
        $ordenadas = $libres->sortBy('numero')->values();

        for ($largo = 1; $largo <= $maxMesas; $largo++) {
            for ($i = 0; $i + $largo <= $ordenadas->count(); $i++) {
                $grupo = $ordenadas->slice($i, $largo);

                if (! $this->sonConsecutivas($grupo)) {
                    continue;
                }

                $capacidadTotal = (int) $grupo->sum('capacidad');
                if ($capacidadTotal >= $personas) {
                    return $grupo->values();
                }
            }
        }

        return null;
    }

    private function sonConsecutivas(Collection $grupo): bool
    {
        if ($grupo->count() < 2) {
            return true;
        }

        $numeros = $grupo->pluck('numero')->all();
        for ($i = 1; $i < count($numeros); $i++) {
            if ($numeros[$i] !== $numeros[$i - 1] + 1) {
                return false;
            }
        }

        return true;
    }
}
