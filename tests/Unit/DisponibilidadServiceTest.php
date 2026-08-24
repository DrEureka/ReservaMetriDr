<?php

namespace Tests\Unit;

use App\Models\Mesa;
use App\Models\Reserva;
use App\Services\DisponibilidadService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DisponibilidadServiceTest extends TestCase
{
    use RefreshDatabase;

    private DisponibilidadService $servicio;

    private CarbonImmutable $manana;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = $this->app->make(DisponibilidadService::class);
        $this->manana = CarbonImmutable::tomorrow();
    }

    public function test_excluye_mesas_con_reserva_solapada_y_libera_en_el_borde(): void
    {
        $mesa1 = Mesa::factory()->en('A')->conCapacidad(4)->create(['numero' => 1]);
        $mesa2 = Mesa::factory()->en('A')->conCapacidad(4)->create(['numero' => 2]);

        Reserva::factory()->create(['ubicacion' => 'A'])->mesas()->attach($mesa1);

        // Mismo slot: la mesa 1 está ocupada, la 2 libre.
        $libres = $this->servicio->mesasLibres('A', $this->manana, '12:00', desdeCache: false);
        $this->assertSame([$mesa2->id], array_column($libres, 'id'));

        // Borde: la reserva existente termina 14:00, arrancar 14:00 es válido.
        $libresBorde = $this->servicio->mesasLibres('A', $this->manana, '14:00', desdeCache: false);
        $this->assertEqualsCanonicalizing(
            [$mesa1->id, $mesa2->id],
            array_column($libresBorde, 'id')
        );
    }

    public function test_desde_cache_false_ignora_una_cache_envenenada(): void
    {
        $mesa = Mesa::factory()->en('A')->conCapacidad(4)->create(['numero' => 1]);

        $clave = "avail:A:{$this->manana->format('Y-m-d')}:12:00";
        Cache::put($clave, json_encode([['id' => 999, 'capacidad' => 99]]), 60);

        $desdeCache = $this->servicio->mesasLibres('A', $this->manana, '12:00');
        $this->assertSame(999, $desdeCache[0]['id']);

        $directo = $this->servicio->mesasLibres('A', $this->manana, '12:00', desdeCache: false);
        $this->assertSame([$mesa->id], array_column($directo, 'id'));
    }

    public function test_invalidar_elimina_los_slots_de_la_ubicacion_indicada(): void
    {
        $fecha = $this->manana->format('Y-m-d');

        Cache::put("avail:A:{$fecha}:12:00", 'x', 60);
        Cache::put("avail:A:{$fecha}:13:30", 'x', 60);
        Cache::put("avail:B:{$fecha}:12:00", 'x', 60);

        $this->servicio->invalidar('A', $this->manana);

        $this->assertTrue(Cache::missing("avail:A:{$fecha}:12:00"));
        $this->assertTrue(Cache::missing("avail:A:{$fecha}:13:30"));
        $this->assertFalse(Cache::missing("avail:B:{$fecha}:12:00"));
    }

    public function test_hay_solapamiento_detecta_conflictos_de_mesas(): void
    {
        $mesa1 = Mesa::factory()->en('A')->conCapacidad(4)->create(['numero' => 1]);
        $mesa2 = Mesa::factory()->en('A')->conCapacidad(4)->create(['numero' => 2]);

        Reserva::factory()->create(['ubicacion' => 'A'])->mesas()->attach($mesa1);

        // Solapa (13:00-15:00 contra 12:00-14:00 sobre la misma mesa).
        $this->assertTrue(
            $this->servicio->haySolapamiento('A', $this->manana, '13:00', [$mesa1->id])
        );

        // Borde exacto: no solapa.
        $this->assertFalse(
            $this->servicio->haySolapamiento('A', $this->manana, '14:00', [$mesa1->id])
        );

        // Otra mesa libre: no solapa.
        $this->assertFalse(
            $this->servicio->haySolapamiento('A', $this->manana, '13:00', [$mesa2->id])
        );

        // Sin mesas que verificar: nunca solapa.
        $this->assertFalse(
            $this->servicio->haySolapamiento('A', $this->manana, '13:00', [])
        );
    }

    public function test_las_reservas_canceladas_no_ocupan_mesas(): void
    {
        $mesa = Mesa::factory()->en('A')->conCapacidad(4)->create(['numero' => 1]);

        $cancelada = Reserva::factory()->cancelada()->create(['ubicacion' => 'A']);
        $cancelada->mesas()->attach($mesa);

        $libres = $this->servicio->mesasLibres('A', $this->manana, '12:00', desdeCache: false);
        $this->assertSame([$mesa->id], array_column($libres, 'id'));
    }
}
