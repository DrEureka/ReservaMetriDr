<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Models\Reserva;
use App\Services\AsignacionUbicacionService;
use App\Services\DisponibilidadService;
use App\Services\HorarioService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ReservaController extends Controller
{
    public function __construct(
        private HorarioService $horarios,
        private AsignacionUbicacionService $asignador,
        private DisponibilidadService $disponibilidad,
    ) {}

    public function create(): mixed
    {
        return view('reservas.create', [
            'horarioService' => $this->horarios,
        ]);
    }

    public function store(StoreReservaRequest $solicitud): RedirectResponse
    {
        $fecha    = CarbonImmutable::parse($solicitud->input('fecha'));
        $hora     = $solicitud->input('hora_inicio');
        $personas = (int) $solicitud->input('cantidad_personas');

        if (! $this->horarios->esHorarioValido($fecha, $hora)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => trans('reservas.errors.fuera_de_horario'),
            ]);
        }

        if (! $this->horarios->puedeReservarAhora($fecha, $hora)) {
            return back()->withInput()->withErrors([
                'hora_inicio' => trans('reservas.errors.muy_tarde', [
                    'minutos' => $this->horarios->anticipacionMinimaMinutos(),
                ]),
            ]);
        }

        $asignacion = $this->asignador->asignar($fecha, $hora, $personas);

        if ($asignacion === null) {
            return back()->withInput()->withErrors([
                'fecha' => trans('reservas.errors.sin_mesas', ['personas' => $personas]),
            ]);
        }

        $llaveLock = "avail_lock:{$asignacion['ubicacion']}:{$fecha->format('Y-m-d')}:{$hora}";
        $lockOk    = Redis::set($llaveLock, '1', 'NX', 'EX', 5);

        if (! $lockOk) {
            return back()->withInput()->withErrors([
                'fecha' => 'Hay otra reserva en proceso, reintentá en unos segundos.',
            ]);
        }

        try {
            $reserva = DB::transaction(function () use ($solicitud, $asignacion, $fecha, $hora, $personas) {
                $r = Reserva::create([
                    'user_id'           => $solicitud->user()->id,
                    'fecha'             => $fecha->format('Y-m-d'),
                    'hora_inicio'       => $hora,
                    'hora_fin'          => $this->horarios->calcularHoraFin($hora),
                    'ubicacion'         => $asignacion['ubicacion'],
                    'cantidad_personas' => $personas,
                    'estado'            => Reserva::ESTADO_CONFIRMADA,
                ]);

                $r->mesas()->attach(
                    collect($asignacion['mesas'])->pluck('id')->all()
                );

                return $r;
            });

            foreach (['A', 'B', 'C', 'D'] as $u) {
                $this->disponibilidad->invalidar($u, $fecha);
            }
        } finally {
            Redis::del($llaveLock);
        }

        return redirect()
            ->route('reservas.mis-reservas')
            ->with('exito', "Reserva #{$reserva->id} creada en sección {$asignacion['ubicacion']}.");
    }
}
