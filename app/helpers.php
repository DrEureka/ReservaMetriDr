<?php

/*
|--------------------------------------------------------------------------
| Helpers — ReservaMetriDr
|--------------------------------------------------------------------------
|
| Funciones globales para formato argentino (fechas, moneda, números).
| Cargadas vía composer.json `autoload.files`.
|
*/

if (! function_exists('fechaAR')) {
    function fechaAR(\DateTimeInterface|string|null $fecha, bool $conHora = false): string
    {
        if (is_null($fecha)) {
            return '';
        }

        if (is_string($fecha)) {
            try {
                $fecha = \Carbon\Carbon::parse($fecha);
            } catch (\Throwable) {
                return '';
            }
        }

        $patron = $conHora ? 'dd/MM/yy HH:mm' : 'dd/MM/yy';

        return \IntlDateFormatter::create(
            'es_AR',
            \IntlDateFormatter::SHORT,
            $conHora ? \IntlDateFormatter::SHORT : \IntlDateFormatter::NONE,
            $fecha->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN,
            $patron
        )->format($fecha->getTimestamp());
    }
}

if (! function_exists('pesoARS')) {
    function pesoARS(float|int|string|null $monto, int $decimales = 2): string
    {
        if (is_null($monto) || $monto === '') {
            return '';
        }

        $formatter = new \NumberFormatter('es_AR', \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency((float) $monto, 'ARS');
    }
}

if (! function_exists('numeroAR')) {
    function numeroAR(float|int|null $numero, int $decimales = 2): string
    {
        $formatter = new \NumberFormatter('es_AR', \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimales);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimales);

        return $formatter->format((float) $numero);
    }
}
