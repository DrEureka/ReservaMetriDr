<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;

class HorarioService
{
    public function dias(): array
    {
        return [
            0 => 'dom',
            1 => 'lun',
            2 => 'mar',
            3 => 'mie',
            4 => 'jue',
            5 => 'vie',
            6 => 'sab',
        ];
    }

    public function nombreDia(int $numeroDia): string
    {
        return $this->dias()[$numeroDia] ?? 'desconocido';
    }

    public function rangoParaFecha(CarbonImmutable $fecha): ?array
    {
        $horarios = Config::get('reservas.horarios_por_dia');
        $numeroDia = (int) $fecha->format('w');

        if (! isset($horarios[$numeroDia])) {
            return null;
        }

        return $horarios[$numeroDia];
    }

    public function duracionMinutos(): int
    {
        return (int) Config::get('reservas.duracion_minutos', 120);
    }

    public function anticipacionMinimaMinutos(): int
    {
        return (int) Config::get('reservas.anticipacion_minima_minutos', 15);
    }

    public function maxMesasPorReserva(): int
    {
        return (int) Config::get('reservas.max_mesas_por_reserva', 3);
    }

    public function calcularHoraFin(string $horaInicio): string
    {
        $inicio = CarbonImmutable::createFromFormat('H:i', $horaInicio);
        $fin    = $inicio->addMinutes($this->duracionMinutos());

        return $fin->format('H:i');
    }

    public function esHorarioValido(CarbonImmutable $fecha, string $horaInicio): bool
    {
        $rango = $this->rangoParaFecha($fecha);
        if ($rango === null) {
            return false;
        }

        $inicio = CarbonImmutable::createFromFormat('H:i', $horaInicio);
        $fin    = $inicio->addMinutes($this->duracionMinutos());

        $limiteInicio = CarbonImmutable::createFromFormat('H:i', $rango['inicio']);
        $limiteFin    = CarbonImmutable::createFromFormat('H:i', $rango['fin']);

        if ($inicio->lessThan($limiteInicio)) {
            return false;
        }

        if ($rango['cruza_medianoche'] ?? false) {
            return $inicio->lessThanOrEqualTo($limiteFin->addDay()) && $fin->lessThanOrEqualTo($limiteFin->addDay());
        }

        return $fin->lessThanOrEqualTo($limiteFin);
    }

    public function fechaInicioReserva(CarbonImmutable $fecha, string $horaInicio): CarbonImmutable
    {
        return $fecha->setTimeFromTimeString($horaInicio);
    }

    public function fechaFinReserva(CarbonImmutable $fecha, string $horaInicio): CarbonImmutable
    {
        $rango = $this->rangoParaFecha($fecha);

        $fin = CarbonImmutable::createFromFormat('H:i', $this->calcularHoraFin($horaInicio));

        if ($rango && ($rango['cruza_medianoche'] ?? false) && $fin->lessThan(CarbonImmutable::createFromFormat('H:i', $horaInicio))) {
            return $fecha->addDay()->setTimeFrom($fin);
        }

        return $fecha->setTimeFrom($fin);
    }

    public function puedeReservarAhora(CarbonImmutable $fecha, string $horaInicio, ?CarbonImmutable $ahora = null): bool
    {
        $ahora = $ahora ?? CarbonImmutable::now();
        $inicioReserva = $this->fechaInicioReserva($fecha, $horaInicio);

        return $inicioReserva->greaterThanOrEqualTo(
            $ahora->addMinutes($this->anticipacionMinimaMinutos())
        );
    }

    public function slotsParaFecha(CarbonImmutable $fecha): array
    {
        $rango = $this->rangoParaFecha($fecha);
        if ($rango === null) {
            return [];
        }

        $slots = [];
        $inicio = CarbonImmutable::createFromFormat('H:i', $rango['inicio']);
        $limite = CarbonImmutable::createFromFormat('H:i', $rango['fin']);
        $paso   = 30;

        while ($inicio->addMinutes($this->duracionMinutos())->lessThanOrEqualTo(
            $rango['cruza_medianoche'] ?? false ? $limite->addDay() : $limite
        )) {
            $slots[] = $inicio->format('H:i');
            $inicio = $inicio->addMinutes($paso);
        }

        return $slots;
    }
}
