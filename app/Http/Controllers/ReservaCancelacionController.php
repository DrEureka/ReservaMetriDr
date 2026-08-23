<?php

namespace App\Http\Controllers;

use App\Mail\ReservaCancelada;
use App\Models\Reserva;
use App\Services\DisponibilidadService;
use App\Services\HorarioService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservaCancelacionController extends Controller
{
    public function __construct(
        private HorarioService $horarios,
        private DisponibilidadService $disponibilidad,
    ) {}

    public function cancelar(Reserva $reserva): RedirectResponse
    {
        $this->asegurarAutor($reserva);

        if ($reserva->estaCancelada()) {
            return back()->with('exito', __('La reserva ya estaba cancelada.'));
        }

        $fechaInicio = $this->horarios->fechaInicioReserva(
            CarbonImmutable::parse($reserva->fecha),
            substr((string) $reserva->hora_inicio, 0, 5)
        );

        if ($fechaInicio->lessThanOrEqualTo(CarbonImmutable::now()->addMinutes(30))) {
            return back()->withErrors([
                'fecha' => __('No se puede cancelar con menos de 30 minutos de anticipación.'),
            ]);
        }

        $fecha = CarbonImmutable::parse($reserva->fecha);

        DB::transaction(function () use ($reserva) {
            $reserva->update([
                'estado'       => Reserva::ESTADO_CANCELADA,
                'cancelada_at' => now(),
            ]);
            $reserva->mesas()->detach();
        });

        foreach (['A', 'B', 'C', 'D'] as $u) {
            $this->disponibilidad->invalidar($u, $fecha);
        }

        Mail::to($reserva->usuario->email)
            ->send((new ReservaCancelada($reserva))->onQueue('emails'));

        if (auth()->check()) {
            return redirect()
                ->route('reservas.mis-reservas')
                ->with('exito', __('Reserva #') . $reserva->id . __(' cancelada correctamente.'));
        }

        return redirect()
            ->route('reservas.create')
            ->with('exito', __('Reserva #') . $reserva->id . __(' cancelada correctamente.'));
    }

    private function asegurarAutor(Reserva $reserva): void
    {
        $usuario = auth()->user();

        if ($usuario && $usuario->esAdmin()) {
            return;
        }

        if ($usuario) {
            abort_unless($reserva->user_id === $usuario->id, 403);
            return;
        }

        // Link firmado desde email (invitado sin sesión)
    }
}
