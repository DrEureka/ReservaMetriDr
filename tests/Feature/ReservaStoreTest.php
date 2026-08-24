<?php

namespace Tests\Feature;

use App\Mail\ReservaConfirmada;
use App\Models\Mesa;
use App\Models\Reserva;
use App\Models\User;
use App\Services\DisponibilidadService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservaStoreTest extends TestCase
{
    use RefreshDatabase;

    private CarbonImmutable $manana;

    private string $fechaManana;

    protected function setUp(): void
    {
        parent::setUp();

        // Lunes 10:00 fijo para que los horarios sean deterministas
        // (lun-vie abren 10:00; sábado cruza medianoche desde las 22:00).
        CarbonImmutable::setTestNow('2026-08-24 10:00:00');

        $this->manana = CarbonImmutable::tomorrow();
        $this->fechaManana = $this->manana->format('Y-m-d');

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => true]),
        ]);
        Mail::fake();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    private function payload(array $reemplazos = []): array
    {
        return array_merge([
            'nombre'            => 'Juan Pérez',
            'email'             => 'juan@example.com',
            'cf-turnstile-response' => 'token-valido',
            'fecha'             => $this->fechaManana,
            'hora_inicio'       => '12:00',
            'cantidad_personas' => 4,
        ], $reemplazos);
    }

    private function sembrarMesasSeccionA(): array
    {
        return [
            Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 1]),
            Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 2]),
            Mesa::factory()->en('A')->conCapacidad(3)->create(['numero' => 3]),
        ];
    }

    public function test_un_invitado_crea_reserva_con_usuario_invitado_y_mesas_optimas(): void
    {
        [$mesa1, $mesa2] = $this->sembrarMesasSeccionA();

        $disponibilidad = $this->app->make(DisponibilidadService::class);
        $disponibilidad->mesasLibres('A', $this->manana, '12:00');
        $clave = "avail:A:{$this->fechaManana}:12:00";
        $this->assertFalse(Cache::missing($clave));

        $respuesta = $this->post(route('reservas.store'), $this->payload());

        $respuesta->assertRedirect(route('reservas.mis-reservas'));
        $respuesta->assertSessionHas('exito');

        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);

        $usuario = User::where('email', 'juan@example.com')->firstOrFail();
        $reserva = Reserva::sole();

        $this->assertSame(Reserva::ESTADO_CONFIRMADA, $reserva->estado);
        $this->assertSame('A', $reserva->ubicacion);
        $this->assertSame(4, $reserva->cantidad_personas);
        $this->assertSame($usuario->id, $reserva->user_id);

        // 4 personas con mesas de capacidad 2+2+3: la corrida óptima usa 1 y 2.
        $this->assertEqualsCanonicalizing(
            [$mesa1->id, $mesa2->id],
            $reserva->mesas()->pluck('mesas.id')->all()
        );

        Mail::assertQueued(ReservaConfirmada::class);

        // La creación debe haber invalidado la disponibilidad cacheada.
        $this->assertTrue(Cache::missing($clave));
    }

    public function test_un_usuario_registrado_reutiliza_su_cuenta(): void
    {
        $this->sembrarMesasSeccionA();

        $usuario = User::factory()->create(['role' => 'cliente']);

        $respuesta = $this->actingAs($usuario)->post(route('reservas.store'), $this->payload([
            'nombre'                => null,
            'email'                 => null,
            'cf-turnstile-response' => null,
        ]));

        $respuesta->assertRedirect(route('reservas.mis-reservas'));

        $reserva = Reserva::sole();
        $this->assertSame($usuario->id, $reserva->user_id);
        $this->assertSame(1, User::count());
    }

    public function test_sin_disponibilidad_responde_422_en_json(): void
    {
        // Una reserva previa ocupa TODAS las mesas del slot.
        Reserva::factory()->conMesas(3)->create([
            'ubicacion'   => 'A',
            'hora_inicio' => '12:00',
            'hora_fin'    => '14:00',
        ]);

        $respuesta = $this->postJson(route('reservas.store'), $this->payload());

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors(['fecha']);
        $this->assertSame(1, Reserva::count());
    }

    public function test_rechaza_horarios_fuera_del_rango_de_atencion(): void
    {
        $respuesta = $this->postJson(route('reservas.store'), $this->payload([
            'hora_inicio' => '09:00',
        ]));

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors(['hora_inicio']);
        $this->assertSame(0, Reserva::count());
    }

    public function test_rechaza_por_anticipacion_minima_insuficiente(): void
    {
        $respuesta = $this->postJson(route('reservas.store'), $this->payload([
            'fecha'       => '2026-08-24',
            'hora_inicio' => '10:05',
        ]));

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors(['hora_inicio']);
        $this->assertSame(0, Reserva::count());
    }

    public function test_el_lock_de_slot_impide_reservas_simultaneas(): void
    {
        $this->sembrarMesasSeccionA();

        $llaveLock = "avail_lock:{$this->fechaManana}:12:00";
        $this->assertTrue(Cache::add($llaveLock, '1', 5));

        $respuesta = $this->post(route('reservas.store'), $this->payload());

        $respuesta->assertRedirect();
        $respuesta->assertSessionHasErrors('fecha');
        $this->assertSame(0, Reserva::count());

        Cache::forget($llaveLock);
    }
}
