<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Mail\ReservaConfirmada;
use App\Models\Reserva;
use App\Models\User;
use App\Services\AsignacionUbicacionService;
use App\Services\DisponibilidadService;
use App\Services\HorarioService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ReservaController extends Controller
{
    public function __construct(
        private HorarioService $horarios,
        private AsignacionUbicacionService $asignador,
        private DisponibilidadService $disponibilidad,
    ) {}

    public function create(): View
    {
        return view('reservas.create', [
            'horarioService' => $this->horarios,
        ]);
    }

    public function store(StoreReservaRequest $solicitud): RedirectResponse|JsonResponse
    {
        $esAjax = $solicitud->expectsJson();

        if (! $solicitud->user()) {
            $this->validarTurnstile($solicitud);
        }

        $fecha    = CarbonImmutable::parse($solicitud->input('fecha'));
        $hora     = $solicitud->input('hora_inicio');
        $personas = (int) $solicitud->input('cantidad_personas');

        if (! $this->horarios->esHorarioValido($fecha, $hora)) {
            return $this->errorForm($solicitud, $esAjax, 'hora_inicio', trans('reservas.errors.fuera_de_horario'));
        }

        if (! $this->horarios->puedeReservarAhora($fecha, $hora)) {
            return $this->errorForm($solicitud, $esAjax, 'hora_inicio', trans('reservas.errors.muy_tarde', [
                'minutos' => $this->horarios->anticipacionMinimaMinutos(),
            ]));
        }

        $asignacion = $this->asignador->asignar($fecha, $hora, $personas);

        if ($asignacion === null) {
            return $this->errorForm($solicitud, $esAjax, 'fecha', trans('reservas.errors.sin_mesas', ['personas' => $personas]));
        }

        $llaveLock = "avail_lock:{$asignacion['ubicacion']}:{$fecha->format('Y-m-d')}:{$hora}";

        $lockOk = false;
        try {
            $lockOk = Cache::store('upstash-rest')->add($llaveLock, '1', 5);
        } catch (\Throwable) {}
        if (! $lockOk) {
            $lockOk = Cache::add($llaveLock, '1', 5);
        }

        if (! $lockOk) {
            return $this->errorForm($solicitud, $esAjax, 'fecha', 'Hay otra reserva en proceso, reintentá en unos segundos.');
        }

        try {
            $reserva = DB::transaction(function () use ($solicitud, $asignacion, $fecha, $hora, $personas) {
                $usuario = $solicitud->user() ?? $this->buscarOCrearInvitado($solicitud);

                $r = Reserva::create([
                    'user_id'           => $usuario->id,
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
            try { Cache::store('upstash-rest')->forget($llaveLock); } catch (\Throwable) {}
            Cache::forget($llaveLock);
        }

        Mail::to($reserva->usuario->email)
            ->send(
                (new ReservaConfirmada($reserva, $this->urlCancelar($reserva)))
                    ->onQueue('emails')
            );

        $mensaje = "Reserva #{$reserva->id} creada en sección {$asignacion['ubicacion']}.";

        if ($esAjax) {
            return response()->json([
                'message' => $mensaje,
                'reserva_id' => $reserva->id,
                'ubicacion' => $asignacion['ubicacion'],
            ]);
        }

        return redirect()
            ->route('reservas.mis-reservas')
            ->with('exito', $mensaje);
    }

    public function misReservas(Request $solicitud): View
    {
        $query = null;

        if (Auth::check()) {
            $query = Reserva::where('user_id', Auth::id());
        } elseif ($solicitud->isMethod('post') && $solicitud->filled('email')) {
            $email = $solicitud->input('email');
            $solicitud->validate(['email' => ['required', 'email']]);
            $usuario = User::where('email', $email)->first();

            if (! $usuario) {
                return view('reservas.mis-reservas', [
                    'reservas' => null,
                    'email_buscado' => $email,
                    'no_encontrado' => true,
                ]);
            }
            $query = Reserva::where('user_id', $usuario->id);
        } else {
            return view('reservas.mis-reservas', [
                'reservas' => null,
                'email_buscado' => null,
                'no_encontrado' => false,
            ]);
        }

        $reservas = $query->orderByDesc('id')
            ->with('mesas')
            ->paginate(15);

        return view('reservas.mis-reservas', [
            'reservas' => $reservas,
            'email_buscado' => $solicitud->input('email'),
            'no_encontrado' => false,
        ]);
    }

    private function buscarOCrearInvitado(StoreReservaRequest $solicitud): User
    {
        $email = $solicitud->input('email');

        $usuario = User::where('email', $email)->first();
        if ($usuario) {
            if ($usuario->name !== $solicitud->input('nombre') && $solicitud->filled('nombre')) {
                $usuario->name = $solicitud->input('nombre');
                $usuario->save();
            }
            return $usuario;
        }

        return User::create([
            'name'     => $solicitud->input('nombre'),
            'email'    => $email,
            'password' => Hash::make(bin2hex(random_bytes(16))),
            'role'     => User::ROL_CLIENTE,
        ]);
    }

    private function errorForm(StoreReservaRequest $solicitud, bool $esAjax, string $campo, string $mensaje): RedirectResponse|JsonResponse
    {
        if ($esAjax) {
            return response()->json(['errors' => [$campo => $mensaje]], 422);
        }

        return back()->withInput()->withErrors([$campo => $mensaje]);
    }

    private function urlCancelar(Reserva $reserva): string
    {
        return URL::signedRoute('reservas.cancelar', ['reserva' => $reserva->id], now()->addDays(7));
    }

    private function validarTurnstile(StoreReservaRequest $solicitud): void
    {
        $token = $solicitud->input('cf-turnstile-response');

        if (! $token) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cf-turnstile-response' => __('Completá el captcha.'),
            ]);
        }

        $respuesta = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => config('services.turnstile.secret'),
            'response' => $token,
            'remoteip' => $solicitud->ip(),
        ]);

        if (! $respuesta->json('success', false)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cf-turnstile-response' => __('Verificación anti-bot fallida. Intentá de nuevo.'),
            ]);
        }
    }
}