<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $solicitud, Closure $siguiente): Response
    {
        $locale = $solicitud->query('lang')
            ?? $solicitud->cookie('locale')
            ?? $solicitud->getPreferredLanguage(['es', 'en']);

        if (! in_array($locale, ['es', 'en'], true)) {
            $locale = config('app.locale', 'es');
        }

        app()->setLocale($locale);

        $respuesta = $siguiente($solicitud);

        if ($solicitud->query('lang') !== null) {
            cookie()->queue(
                cookie('locale', $locale, 60 * 24 * 30, '/', null, false, false)
            );
        }

        return $respuesta;
    }
}
