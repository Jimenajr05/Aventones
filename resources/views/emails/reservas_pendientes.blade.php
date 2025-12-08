@php
    // Obtener el chofer y la cantidad de reservas
    $primeraReserva = $reservas->first();
    $chofer = $primeraReserva->ride->vehiculo->chofer;
    $cantidad = $reservas->count();
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
        li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <p class="title">Hola {{ $chofer->nombre }},</p>

    {{-- Título dinámico para reflejar la cantidad consolidada --}}
    @if ($cantidad > 1)
        <p>Tienes <span style="font-weight: bold; color: #E36414;">{{ $cantidad }}</span> reservas pendientes de revisión:</p>
    @else
        <p>Tienes la siguiente reserva pendiente de revisión:</p>
    @endif

    {{-- Iterar sobre la colección de reservas --}}
    <ul>
        @foreach ($reservas as $reserva)
            @php
                $ride = $reserva->ride;
            @endphp
            <li>
                Reserva #{{ $reserva->id }} de {{ $ride->nombre ?? 'Ride sin nombre' }}
                <strong>(pendiente {{ $reserva->created_at->diffForHumans() }})</strong>
            </li>
        @endforeach
    </ul>

    <br>

    <p>
        Por favor, ingresa a tu cuenta para gestionarlas y aceptarlas o rechazarlas.
    </p>

</body>
</html>
