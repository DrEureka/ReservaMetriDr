<?php

namespace App\Console\Commands;

use App\Services\AsignacionUbicacionService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ReservasDisponibilidad extends Command
{
    protected $signature = 'reservas:disponibilidad {fecha} {hora_inicio} {personas} {--ubicacion=}';

    protected $description = 'Muestra la asignación propuesta de mesas para fecha/hora/cantidad de personas';

    public function handle(AsignacionUbicacionService $asignador): int
    {
        try {
            $fecha = CarbonImmutable::parse($this->argument('fecha'));
        } catch (\Throwable) {
            $this->error('Fecha inválida (formato esperado: YYYY-MM-DD)');
            return self::FAILURE;
        }

        $hora       = $this->argument('hora_inicio');
        $personas   = (int) $this->argument('personas');
        $filtroUb  = $this->option('ubicacion');

        $resultado = $filtroUb
            ? null
            : $asignador->asignar($fecha, $hora, $personas);

        if ($filtroUb) {
            $libres = app(\App\Services\DisponibilidadService::class)
                ->mesasLibres($filtroUb, $fecha, $hora);

            $this->info("Mesas libres en sección $filtroUb para {$fecha->format('Y-m-d')} $hora:");
            foreach ($libres as $mesa) {
                $this->line("  {$mesa['nombre']} - capacidad {$mesa['capacidad']}");
            }
            $this->line("Total: " . count($libres));
            return self::SUCCESS;
        }

        if ($resultado === null) {
            $this->warn("Sin disponibilidad para $personas personas en {$fecha->format('Y-m-d')} $hora");
            return self::SUCCESS;
        }

        $this->info("Asignación propuesta: sección {$resultado['ubicacion']}");
        foreach ($resultado['mesas'] as $mesa) {
            $this->line("  {$mesa['nombre']} (cap {$mesa['capacidad']})");
        }
        $this->line('Capacidad total: ' . collect($resultado['mesas'])->sum('capacidad'));

        return self::SUCCESS;
    }
}
