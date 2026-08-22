<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HorarioService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HorariosController extends Controller
{
    public function __construct(private HorarioService $horarios) {}

    public function slots(Request $solicitud): JsonResponse
    {
        $datos = $solicitud->validate([
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $fecha = CarbonImmutable::parse($datos['fecha']);
        $slots = $this->horarios->slotsParaFecha($fecha);
        $rango = $this->horarios->rangoParaFecha($fecha);

        return response()->json([
            'fecha'  => $fecha->format('Y-m-d'),
            'rango'  => $rango,
            'slots'  => $slots,
            'duracion_minutos' => $this->horarios->duracionMinutos(),
        ]);
    }
}
