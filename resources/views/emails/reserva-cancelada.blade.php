<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .title { font-size: 22px; font-weight: bold; color: #111827; margin-bottom: 16px; }
        .panel { background: #fef2f2; border-left: 4px solid #dc2626; padding: 12px 16px; margin: 16px 0; }
        .panel-label { font-weight: bold; color: #991b1b; }
        .row { margin: 6px 0; }
        .row-label { font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="title">{{ __('Reserva cancelada') }}</div>

    <p>{{ __('Hola :nombre, confirmamos la cancelación de tu reserva.', ['nombre' => $reserva->usuario->name]) }}</p>

    <div class="panel">
        <span class="panel-label">{{ __('Código') }}:</span> #{{ $reserva->id }}
    </div>

    <div class="row"><span class="row-label">{{ __('Fecha que estaba reservada') }}:</span> {{ fechaAR($reserva->fecha) }}</div>
    <div class="row"><span class="row-label">{{ __('Hora') }}:</span> {{ substr($reserva->hora_inicio, 0, 5) }} a {{ substr($reserva->hora_fin, 0, 5) }} hs</div>
    <div class="row"><span class="row-label">{{ __('Ubicación') }}:</span> {{ __('reservas.ubicaciones.' . $reserva->ubicacion) }}</div>

    <p>{{ __('Si querés volver a reservar, ingresá a nuestra plataforma.') }}</p>

    <p>{{ __('¡Gracias!') }}</p>
</div>
</body>
</html>
