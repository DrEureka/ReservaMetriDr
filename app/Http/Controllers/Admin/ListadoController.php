<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ListadoController extends Controller
{
    public function index(Request $solicitud): View
    {
        $datos = $solicitud->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $fecha = isset($datos['fecha'])
            ? CarbonImmutable::parse($datos['fecha'])
            : CarbonImmutable::today();

        $reservas = $this->consultaReservasPorFecha($fecha);

        return view('admin.listado.index', [
            'fecha' => $fecha->format('Y-m-d'),
            'reservas' => $reservas,
            'reservasAgrupadas' => $this->agruparPorUbicacionYTurno($reservas),
            'ubicaciones' => Reserva::UBICACIONES,
        ]);
    }

    private function consultaReservasPorFecha(CarbonImmutable $fecha): array
    {
        $sql = <<<'SQL'
            SELECT
                r.id,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.ubicacion,
                r.cantidad_personas,
                r.estado,
                r.cancelada_at,
                r.created_at,
                u.name AS cliente_nombre,
                u.email AS cliente_email,
                GROUP_CONCAT(
                    CONCAT(m.ubicacion, '-', m.numero)
                    ORDER BY m.numero ASC
                    SEPARATOR ', '
                ) AS mesas
            FROM reservas AS r
            INNER JOIN users AS u ON u.id = r.user_id
            INNER JOIN reserva_mesa AS rm ON rm.reserva_id = r.id
            INNER JOIN mesas AS m ON m.id = rm.mesa_id
            WHERE r.fecha = ?
            GROUP BY r.id,
                     r.fecha,
                     r.hora_inicio,
                     r.hora_fin,
                     r.ubicacion,
                     r.cantidad_personas,
                     r.estado,
                     r.cancelada_at,
                     r.created_at,
                     u.id,
                     u.name,
                     u.email
            ORDER BY r.ubicacion ASC,
                     FIELD(r.ubicacion, 'A','B','C','D'),
                     r.hora_inicio ASC
        SQL;

        return DB::select($sql, [$fecha->format('Y-m-d')]);
    }

    private function agruparPorUbicacionYTurno(array $reservas): array
    {
        $turnos = [
            'manana' => ['inicio' => '00:00', 'fin' => '13:00'],
            'tarde'  => ['inicio' => '13:00', 'fin' => '18:00'],
            'noche'  => ['inicio' => '18:00', 'fin' => '23:59'],
        ];

        $grupos = [];
        foreach (Reserva::UBICACIONES as $ubi) {
            $grupos[$ubi] = ['manana' => [], 'tarde' => [], 'noche' => []];
        }

        foreach ($reservas as $r) {
            $hora = substr((string) $r->hora_inicio, 0, 5);
            $turno = 'noche';
            foreach ($turnos as $clave => $rango) {
                if ($hora >= $rango['inicio'] && $hora < $rango['fin']) {
                    $turno = $clave;
                    break;
                }
            }
            $grupos[$r->ubicacion][$turno][] = $r;
        }

        return $grupos;
    }
}
