<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .title { font-size: 22px; font-weight: bold; color: #111827; margin-bottom: 16px; }
        .panel { background: #f3f4f6; border-left: 4px solid #6b7280; padding: 12px 16px; margin: 16px 0; }
        .panel-label { font-weight: bold; color: #374151; }
        .row { margin: 6px 0; }
        .row-label { font-weight: bold; }
        .button { display: inline-block; padding: 12px 24px; margin: 16px 0; background: #dc2626; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .subcopy { font-size: 13px; color: #6b7280; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="title">{{ __('Reserva confirmada') }}</div>

    <p>{{ __('Hola :nombre, te confirmamos tu reserva.', ['nombre' => $reserva->usuario->name]) }}</p>

    <div class="panel">
        <span class="panel-label">{{ __('Código') }}:</span> #{{ $reserva->id }}
    </div>

    <div class="row"><span class="row-label">{{ __('Fecha') }}:</span> {{ fechaAR($reserva->fecha) }}</div>
    <div class="row"><span class="row-label">{{ __('Hora') }}:</span> {{ substr($reserva->hora_inicio, 0, 5) }} a {{ substr($reserva->hora_fin, 0, 5) }} hs</div>
    <div class="row"><span class="row-label">{{ __('Ubicación') }}:</span> {{ __('reservas.ubicaciones.' . $reserva->ubicacion) }}</div>
    <div class="row"><span class="row-label">{{ __('Mesas') }}:</span> {{ $reserva->nombresMesas() }}</div>
    <div class="row"><span class="row-label">{{ __('Personas') }}:</span> {{ $reserva->cantidad_personas }}</div>

    <p>{{ __('Si necesitás cancelar tu reserva, podés hacerlo hasta 30 minutos antes del horario reservado.') }}</p>

    <a href="{{ $urlCancelar }}" class="button">{{ __('Cancelar mi reserva') }}</a>

    <div class="subcopy">
        {{ __('Si el botón no funciona, copiá y pegá este enlace en tu navegador') }}:<br>
        <a href="{{ $urlCancelar }}">{{ $urlCancelar }}</a>
    </div>

    <p>{{ __('Te esperamos. ¡Gracias!') }}</p>
</div>
</body>
</html>
