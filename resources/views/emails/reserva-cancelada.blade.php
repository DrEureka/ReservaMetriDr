<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reserva cancelada') }} · ReservaMetriDr</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0F1614;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            background-color: #0F1614;
            padding: 40px 20px;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #1A2320;
            border-radius: 12px;
            overflow: hidden;
        }
        .header {
            background-color: #374151;
            padding: 32px 40px;
            text-align: center;
        }
        .logo {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 28px;
            font-weight: 600;
            color: #E8E0D4;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .tagline {
            font-size: 11px;
            color: #9C9688;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 4px 0 0 0;
        }
        .body {
            padding: 40px;
        }
        .greeting {
            font-size: 15px;
            color: #E8E0D4;
            margin: 0 0 32px 0;
            line-height: 1.6;
        }
        .code-badge {
            display: inline-block;
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            border-radius: 8px;
            padding: 12px 24px;
            margin-bottom: 32px;
        }
        .code-label {
            font-size: 11px;
            color: #9C9688;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: block;
            margin-bottom: 4px;
        }
        .code-number {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: 700;
            color: #DC2626;
            letter-spacing: 2px;
        }
        .details {
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-row:first-child {
            padding-top: 0;
        }
        .detail-label {
            font-size: 12px;
            color: #9C9688;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 140px;
            flex-shrink: 0;
        }
        .detail-value {
            font-size: 15px;
            color: #E8E0D4;
            font-weight: 500;
        }
        .notice {
            font-size: 13px;
            color: #9C9688;
            line-height: 1.6;
            margin: 0 0 32px 0;
            padding: 16px;
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            border-left: 3px solid #6B7280;
        }
        .button {
            display: inline-block;
            background-color: #D4A35C;
            color: #0F1614;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.2s ease;
        }
        .button:hover {
            background-color: #C4954D;
        }
        .closing {
            font-size: 14px;
            color: #E8E0D4;
            margin: 0;
            font-style: italic;
        }
        .footer {
            padding: 32px 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            text-align: center;
        }
        .footer-text {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 20px 12px; }
            .body { padding: 28px 24px; }
            .header { padding: 24px; }
            .logo { font-size: 22px; }
            .detail-row { flex-direction: column; gap: 4px; }
            .detail-label { width: auto; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">
        <div class="header">
            <h1 class="logo">ReservaMetriDr</h1>
            <p class="tagline">{{ __('Cancelación confirmada') }}</p>
        </div>

        <div class="body">
            <p class="greeting">{{ __('Hola :nombre, confirmamos la cancelación de tu reserva.', ['nombre' => $reserva->usuario->name]) }}</p>

            <div class="code-badge">
                <span class="code-label">{{ __('Reserva cancelada') }}</span>
                <span class="code-number">#{{ $reserva->id }}</span>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">{{ __('Fecha original') }}</span>
                    <span class="detail-value">{{ fechaAR($reserva->fecha) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Horario') }}</span>
                    <span class="detail-value">{{ substr($reserva->hora_inicio, 0, 5) }} – {{ substr($reserva->hora_fin, 0, 5) }} hs</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Sección') }}</span>
                    <span class="detail-value">{{ __('reservas.ubicaciones.' . $reserva->ubicacion) }}</span>
                </div>
            </div>

            <p class="notice">{{ __('Si querés volver a reservar, ingresá a nuestra plataforma cuando quieras.') }}</p>

            <div style="text-align: center;">
                <a href="{{ route('reservas.create') }}" class="button">{{ __('Hacer una reserva') }}</a>
            </div>

            <p class="closing">{{ __('¡Te esperamos cuando quieras!') }}</p>
        </div>

        <div class="footer">
            <p class="footer-text">ReservaMetriDr · {{ date('Y') }}</p>
        </div>
    </div>
</div>
</body>
</html>