{{ __('Hola :nombre, confirmamos la cancelación de tu reserva.', ['nombre' => $reserva->usuario->name]) }}

{{ __('Código') }}: #{{ $reserva->id }}
{{ __('Fecha que estaba reservada') }}: {{ fechaAR($reserva->fecha) }}
{{ __('Hora') }}: {{ substr($reserva->hora_inicio, 0, 5) }} a {{ substr($reserva->hora_fin, 0, 5) }} hs
{{ __('Ubicación') }}: {{ __('reservas.ubicaciones.' . $reserva->ubicacion) }}

{{ __('Si querés volver a reservar, ingresá a nuestra plataforma.') }}

{{ __('¡Gracias!') }}
