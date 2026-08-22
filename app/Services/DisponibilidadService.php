<?php

namespace App\Services;

use App\Models\Mesa;
use App\Models\Reserva;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Redis;

class DisponibilidadService
{
    public function __construct(private HorarioService $horarios) {}

    public function mesasLibres(string $ubicacion, CarbonImmutable $fecha, string $horaInicio): array
    {
        $clave = $this->claveCache($ubicacion, $fecha, $horaInicio);

        $cacheadas = Redis::get($clave);
        if ($cacheadas !== null) {
            return json_decode($cacheadas, true) ?? [];
        }

        $libres = $this->calcularMesasLibres($ubicacion, $fecha, $horaInicio);

        $segundosHastaFinDelDia = max(60, $fecha->endOfDay()->diffInSeconds(CarbonImmutable::now()));
        Redis::setex($clave, $segundosHastaFinDelDia, json_encode($libres));

        return $libres;
    }

    public function invalidar(string $ubicacion, CarbonImmutable $fecha): void
    {
        $prefijo = (string) config('database.redis.options.prefix', '');

        foreach (Redis::keys("avail:{$ubicacion}:{$fecha->format('Y-m-d')}:*") as $keyCompleto) {
            $keySinPrefijo = str_starts_with($keyCompleto, $prefijo)
                ? substr($keyCompleto, strlen($prefijo))
                : $keyCompleto;
            Redis::del($keySinPrefijo);
        }
    }

    private function calcularMesasLibres(string $ubicacion, CarbonImmutable $fecha, string $horaInicio): array
    {
        $todasMesas = Mesa::ubicacion($ubicacion)
            ->orderBy('numero')
            ->get();

        $ocupadasEnRango = $this->idsMesasOcupadas($ubicacion, $fecha, $horaInicio);

        return $todasMesas
            ->reject(fn (Mesa $mesa) => in_array($mesa->id, $ocupadasEnRango, true))
            ->map(fn (Mesa $mesa) => [
                'id'        => $mesa->id,
                'ubicacion' => $mesa->ubicacion,
                'numero'    => $mesa->numero,
                'capacidad' => $mesa->capacidad,
                'nombre'    => $mesa->nombreCompleto(),
            ])
            ->values()
            ->all();
    }

    private function idsMesasOcupadas(string $ubicacion, CarbonImmutable $fecha, string $horaInicio): array
    {
        $inicioNuevo = $this->horarios->fechaInicioReserva($fecha, $horaInicio);
        $finNuevo    = $this->horarios->fechaFinReserva($fecha, $horaInicio);

        $candidatas = Reserva::where('fecha', $fecha->format('Y-m-d'))
            ->where('ubicacion', $ubicacion)
            ->where('estado', Reserva::ESTADO_CONFIRMADA)
            ->with('mesas')
            ->get();

        $ocupadas = [];
        foreach ($candidatas as $reserva) {
            $hora = substr((string) $reserva->hora_inicio, 0, 5);
            $reservaInicio = $this->horarios->fechaInicioReserva(
                CarbonImmutable::parse($reserva->fecha),
                $hora
            );
            $reservaFin = $this->horarios->fechaFinReserva(
                CarbonImmutable::parse($reserva->fecha),
                $hora
            );

            if ($reservaInicio->lessThan($finNuevo) && $reservaFin->greaterThan($inicioNuevo)) {
                foreach ($reserva->mesas as $mesa) {
                    $ocupadas[] = $mesa->id;
                }
            }
        }

        return $ocupadas;
    }

    private function claveCache(string $ubicacion, CarbonImmutable $fecha, string $horaInicio): string
    {
        return "avail:{$ubicacion}:{$fecha->format('Y-m-d')}:{$horaInicio}";
    }
}
