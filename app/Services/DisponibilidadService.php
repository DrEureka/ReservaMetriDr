<?php

namespace App\Services;

use App\Models\Mesa;
use App\Models\Reserva;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DisponibilidadService
{
    public function __construct(private HorarioService $horarios) {}

    public function mesasLibres(string $ubicacion, CarbonImmutable $fecha, string $horaInicio, bool $desdeCache = true): array
    {
        if (! $desdeCache) {
            return $this->calcularMesasLibres($ubicacion, $fecha, $horaInicio);
        }

        $clave = $this->claveCache($ubicacion, $fecha, $horaInicio);
        $segundos = max(60, $fecha->endOfDay()->diffInSeconds(CarbonImmutable::now()));

        $cacheadas = $this->cacheGet($clave);
        if ($cacheadas !== null) {
            return json_decode($cacheadas, true) ?? [];
        }

        $libres = $this->calcularMesasLibres($ubicacion, $fecha, $horaInicio);

        $this->cacheSet($clave, json_encode($libres), (int) $segundos);

        return $libres;
    }

    public function invalidar(string $ubicacion, CarbonImmutable $fecha): void
    {
        foreach ($this->horarios->slotsParaFecha($fecha) as $slot) {
            $clave = $this->claveCache($ubicacion, $fecha, $slot);

            try {
                Cache::store('upstash-rest')->forget($clave);
            } catch (\Throwable $e) {
                Log::warning('No se pudo invalidar la clave en Upstash.', [
                    'clave' => $clave,
                    'error' => $e->getMessage(),
                ]);
            }
            Cache::forget($clave);
        }
    }

    public function haySolapamiento(string $ubicacion, CarbonImmutable $fecha, string $horaInicio, array $mesaIds): bool
    {
        if ($mesaIds === []) {
            return false;
        }

        $inicioNuevo = $this->horarios->fechaInicioReserva($fecha, $horaInicio);
        $finNuevo    = $this->horarios->fechaFinReserva($fecha, $horaInicio);

        return Reserva::whereDate('fecha', $fecha->format('Y-m-d'))
            ->where('ubicacion', $ubicacion)
            ->where('estado', Reserva::ESTADO_CONFIRMADA)
            ->whereHas('mesas', fn ($consulta) => $consulta->whereIn('mesas.id', $mesaIds))
            ->with('mesas')
            ->get()
            ->contains(fn (Reserva $reserva) => $this->reservaSolapa($reserva, $inicioNuevo, $finNuevo));
    }

    public function disponibilidadBatch(CarbonImmutable $fecha, array $slots, int $personas): array
    {
        $fechaStr = $fecha->format('Y-m-d');

        $todasMesas = Mesa::orderBy('ubicacion')->orderBy('numero')->get()
            ->groupBy('ubicacion');

        $reservasDelDia = Reserva::whereDate('fecha', $fechaStr)
            ->where('estado', Reserva::ESTADO_CONFIRMADA)
            ->with('mesas')
            ->get();

        $resultado = [];

        foreach ($slots as $hora) {
            $inicioSlot = $this->horarios->fechaInicioReserva($fecha, $hora);
            $finSlot    = $this->horarios->fechaFinReserva($fecha, $hora);

            $mesasOcupadasIds = [];

            foreach ($reservasDelDia as $reserva) {
                if ($this->reservaSolapa($reserva, $inicioSlot, $finSlot)) {
                    foreach ($reserva->mesas as $mesa) {
                        $mesasOcupadasIds[$mesa->id] = true;
                    }
                }
            }

            $totalMesas    = 0;
            $totalCapacidad = 0;

            foreach (Reserva::UBICACIONES as $sec) {
                $libres = collect($todasMesas->get($sec, collect())->all())
                    ->reject(fn (Mesa $m) => isset($mesasOcupadasIds[$m->id]))
                    ->filter(fn (Mesa $m) => $m->capacidad >= $personas);

                $cap = (int) $libres->sum('capacidad');
                $totalMesas    += $libres->count();
                $totalCapacidad += $cap;
            }

            $resultado[] = [
                'hora'               => $hora,
                'disponible'         => $totalMesas > 0,
                'total_mesas_libres' => $totalMesas,
                'total_capacidad'    => $totalCapacidad,
            ];
        }

        return $resultado;
    }

    public function resumenSlot(CarbonImmutable $fecha, string $horaInicio, int $personas): array
    {
        $secciones = [];
        $totalMesas = 0;
        $totalCapacidad = 0;

        foreach (Reserva::UBICACIONES as $ubicacion) {
            $libres = collect($this->mesasLibres($ubicacion, $fecha, $horaInicio))
                ->filter(fn (array $mesa) => $mesa['capacidad'] >= $personas);

            $capacidad = (int) $libres->sum('capacidad');

            $secciones[$ubicacion] = [
                'ubicacion'     => $ubicacion,
                'mesas_libres'  => $libres->count(),
                'capacidad'     => $capacidad,
            ];

            $totalMesas    += $libres->count();
            $totalCapacidad += $capacidad;
        }

        return [
            'disponible'         => $totalMesas > 0,
            'total_mesas_libres' => $totalMesas,
            'total_capacidad'    => $totalCapacidad,
            'secciones'          => array_values($secciones),
        ];
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

        $candidatas = Reserva::whereDate('fecha', $fecha->format('Y-m-d'))
            ->where('ubicacion', $ubicacion)
            ->where('estado', Reserva::ESTADO_CONFIRMADA)
            ->with('mesas')
            ->get();

        $ocupadas = [];
        foreach ($candidatas as $reserva) {
            if ($this->reservaSolapa($reserva, $inicioNuevo, $finNuevo)) {
                foreach ($reserva->mesas as $mesa) {
                    $ocupadas[] = $mesa->id;
                }
            }
        }

        return $ocupadas;
    }

    private function reservaSolapa(Reserva $reserva, CarbonImmutable $inicioNuevo, CarbonImmutable $finNuevo): bool
    {
        $hora = substr((string) $reserva->hora_inicio, 0, 5);
        $reservaFecha = CarbonImmutable::parse($reserva->fecha);

        $reservaInicio = $this->horarios->fechaInicioReserva($reservaFecha, $hora);
        $reservaFin    = $this->horarios->fechaFinReserva($reservaFecha, $hora);

        return $reservaInicio->lessThan($finNuevo) && $reservaFin->greaterThan($inicioNuevo);
    }

    private function claveCache(string $ubicacion, CarbonImmutable $fecha, string $horaInicio): string
    {
        return "avail:{$ubicacion}:{$fecha->format('Y-m-d')}:{$horaInicio}";
    }

    private function cacheGet(string $key): ?string
    {
        try {
            $valor = Cache::store('upstash-rest')->get($key);

            if ($valor !== null) {
                return $valor;
            }
        } catch (\Throwable $e) {
            Log::warning('Lectura de caché Upstash falló, usando caché local.', [
                'clave' => $key,
                'error' => $e->getMessage(),
            ]);
        }

        return Cache::get($key);
    }

    private function cacheSet(string $key, mixed $value, int $ttl): void
    {
        try {
            Cache::store('upstash-rest')->put($key, $value, $ttl);
        } catch (\Throwable $e) {
            Log::warning('Escritura de caché Upstash falló, usando caché local.', [
                'clave' => $key,
                'error' => $e->getMessage(),
            ]);
        }

        Cache::put($key, $value, $ttl);
    }
}
