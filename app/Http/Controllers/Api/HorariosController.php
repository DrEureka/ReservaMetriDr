<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DisponibilidadService;
use App\Services\HorarioService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HorariosController extends Controller
{
    public function __construct(
        private HorarioService $horarios,
        private DisponibilidadService $disponibilidad,
    ) {}

    public function slots(Request $solicitud): JsonResponse
    {
        $datos = $solicitud->validate([
            'fecha'    => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'personas' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $fecha    = CarbonImmutable::parse($datos['fecha']);
        $personas = (int) ($datos['personas'] ?? 2);
        $slotsCrudos = $this->horarios->slotsParaFecha($fecha);
        $rango = $this->horarios->rangoParaFecha($fecha);

        $slots = $this->disponibilidad->disponibilidadBatch($fecha, $slotsCrudos, $personas);

        return response()->json([
            'fecha'             => $fecha->format('Y-m-d'),
            'rango'             => $rango,
            'personas'          => $personas,
            'duracion_minutos'  => $this->horarios->duracionMinutos(),
            'slots'             => $slots,
        ]);
    }
}
