<?php

namespace Tests\Feature;

use App\Mail\ReservaCancelada;
use App\Models\Reserva;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CancelacionReservaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Lunes 10:00 fijo para determinismo horario.
        CarbonImmutable::setTestNow('2026-08-24 10:00:00');
        Mail::fake();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    private function urlFirmada(Reserva $reserva): string
    {
        return URL::signedRoute('reservas.cancelar', ['reserva' => $reserva->id], now()->addDays(7));
    }

    public function test_cancela_desde_un_link_firmado_como_invitado(): void
    {
        $reserva = Reserva::factory()->conMesas(2)->create();

        $respuesta = $this->get($this->urlFirmada($reserva));

        $respuesta->assertRedirect(route('reservas.create'));
        $respuesta->assertSessionHas('exito');

        $reserva->refresh();
        $this->assertTrue($reserva->estaCancelada());
        $this->assertNotNull($reserva->cancelada_at);
        $this->assertSame(0, $reserva->mesas()->count());

        Mail::assertQueued(ReservaCancelada::class);
    }

    public function test_rechaza_un_link_con_firma_invalida(): void
    {
        $reserva = Reserva::factory()->conMesas(2)->create();

        $respuesta = $this->get($this->urlFirmada($reserva).'&signature=firma-manipulada');

        $respuesta->assertForbidden();
        $this->assertFalse($reserva->refresh()->estaCancelada());
    }

    public function test_no_permite_cancelar_con_menos_de_30_minutos_de_anticipacion(): void
    {
        // Ahora son las 10:00; la reserva empieza 10:15 (dentro de la ventana de 30 min).
        $reserva = Reserva::factory()->create([
            'fecha'       => '2026-08-24',
            'hora_inicio' => '10:15',
            'hora_fin'    => '12:15',
        ]);

        $respuesta = $this->from('/')->get($this->urlFirmada($reserva));

        $respuesta->assertRedirect('/');
        $respuesta->assertSessionHasErrors('fecha');
        $this->assertFalse($reserva->refresh()->estaCancelada());
        Mail::assertNothingSent();
    }
}
