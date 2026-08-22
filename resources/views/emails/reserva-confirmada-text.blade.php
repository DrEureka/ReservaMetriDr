{{ __('Hola :nombre, te confirmamos tu reserva.', ['nombre' => $reserva->usuario->name]) }}

{{ __('Código') }}: #{{ $reserva->id }}
{{ __('Fecha') }}: {{ fechaAR($reserva->fecha) }}
{{ __('Hora') }}: {{ substr($reserva->hora_inicio, 0, 5) }} a {{ substr($reserva->hora_fin, 0, 5) }} hs
{{ __('Ubicación') }}: {{ __('reservas.ubicaciones.' . $reserva->ubicacion) }}
{{ __('Mesas') }}: {{ $reserva->nombresMesas() }}
{{ __('Personas') }}: {{ $reserva->cantidad_personas }}

{{ __('Si necesitás cancelar tu reserva, podés hacerlo hasta 30 minutos antes del horario reservado desde este enlace') }}:

{{ $urlCancelar }}

{{ __('Te esperamos. ¡Gracias!') }}
