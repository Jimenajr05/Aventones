@php
    $chofer = $reserva->ride->vehiculo->chofer;
    $pasajero = $reserva->pasajero;
    $ride = $reserva->ride;
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
        }
        ul {
            margin-left: 15px;
        }
    </style>
</head>
<body>

    <p class="title">Hola {{ $chofer->nombre }},</p>

    <p>Tienes las siguientes reservas pendientes de revisión:</p>

    <ul>
        <li>
            Reserva #{{ $reserva->id }} de {{ $ride->nombre ?? 'Ride sin nombre' }}
            <strong>(pendiente {{ $reserva->created_at->diffForHumans() }})</strong>
        </li>
    </ul>

    <br>

    <p>
        Por favor, ingresa a tu cuenta para gestionarlas.
    </p>

</body>
</html>
