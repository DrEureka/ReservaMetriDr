<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAdmin
{
    public function handle(Request $solicitud, Closure $siguiente): Response
    {
        $usuario = $solicitud->user();

        if (! $usuario || ! $usuario->esAdmin()) {
            abort(403, 'No tenés permisos para acceder a esta sección.');
        }

        return $siguiente($solicitud);
    }
}
