<?php

namespace Tests\Unit;

use App\Models\Mesa;
use App\Services\AsignacionUbicacionService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AsignacionUbicacionServiceTest extends TestCase
{
    use RefreshDatabase;

    private AsignacionUbicacionService $servicio;

    private CarbonImmutable $manana;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = $this->app->make(AsignacionUbicacionService::class);
        $this->manana = CarbonImmutable::tomorrow();
    }

    private function mesasSeccionA(): array
    {
        return [
            Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 1]),
            Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 2]),
            Mesa::factory()->en('A')->conCapacidad(3)->create(['numero' => 3]),
        ];
    }

    public function test_elige_la_mesa_individual_mas_chica_que_alcanza(): void
    {
        [, , $mesa3] = $this->mesasSeccionA();
        Mesa::factory()->en('B')->conCapacidad(6)->create(['numero' => 1]);

        $asignacion = $this->servicio->asignar($this->manana, '12:00', 3, desdeCache: false);

        $this->assertNotNull($asignacion);
        $this->assertSame('A', $asignacion['ubicacion']);
        $this->assertCount(1, $asignacion['mesas']);
        $this->assertSame($mesa3->id, $asignacion['mesas'][0]['id']);
    }

    public function test_combina_corrida_consecutiva_cuando_las_individuales_no_alcanzan(): void
    {
        [$mesa1, $mesa2] = $this->mesasSeccionA();

        $asignacion = $this->servicio->asignar($this->manana, '12:00', 4, desdeCache: false);

        $this->assertNotNull($asignacion);
        $this->assertSame('A', $asignacion['ubicacion']);
        $this->assertEqualsCanonicalizing(
            [$mesa1->id, $mesa2->id],
            collect($asignacion['mesas'])->pluck('id')->all()
        );
    }

    public function test_salta_secciones_sin_capacidad_suficiente(): void
    {
        $mesaB = Mesa::factory()->en('B')->conCapacidad(6)->create(['numero' => 1]);

        $asignacion = $this->servicio->asignar($this->manana, '12:00', 5, desdeCache: false);

        $this->assertNotNull($asignacion);
        $this->assertSame('B', $asignacion['ubicacion']);
        $this->assertSame([$mesaB->id], collect($asignacion['mesas'])->pluck('id')->all());
    }

    public function test_respeta_el_maximo_de_mesas_por_reserva(): void
    {
        Config::set('reservas.max_mesas_por_reserva', 2);

        Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 1]);
        Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 2]);
        Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 3]);

        $asignacion = $this->servicio->asignar($this->manana, '12:00', 5, desdeCache: false);

        $this->assertNull($asignacion);
    }

    public function test_devuelve_null_cuando_ninguna_mesa_alcanza(): void
    {
        Mesa::factory()->en('A')->conCapacidad(2)->create(['numero' => 1]);

        $asignacion = $this->servicio->asignar($this->manana, '12:00', 50, desdeCache: false);

        $this->assertNull($asignacion);
    }
}
