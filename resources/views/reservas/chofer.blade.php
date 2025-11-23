<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reservas Recibidas
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto">

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white p-6 shadow rounded">

            <h3 class="text-xl font-bold mb-4">Solicitudes recibidas</h3>

            @if ($reservas->isEmpty())
                <p class="text-gray-600">No tienes solicitudes de reserva.</p>
            @else

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-200 text-left">
                                <th class="p-2">Pasajero</th>
                                <th class="p-2">Ride</th>
                                <th class="p-2">Fecha</th>
                                <th class="p-2">Espacios</th>
                                <th class="p-2">Estado</th>
                                <th class="p-2">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($reservas as $reserva)
                                <tr class="border-b">

                                    {{-- Pasajero --}}
                                    <td class="p-2">
                                        {{ $reserva->pasajero->nombre }} 
                                        {{ $reserva->pasajero->apellido }}
                                    </td>

                                    {{-- Nombre del Ride --}}
                                    <td class="p-2">
                                        {{ $reserva->ride->nombre }}
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="p-2">
                                        {{ \Carbon\Carbon::parse($reserva->ride->fecha)->format('d/m/Y') }}
                                        - {{ $reserva->ride->hora }}
                                    </td>

                                    {{-- Espacios --}}
                                    <td class="p-2">
                                        {{ $reserva->espacios ?? 1 }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="p-2">
                                        @if ($reserva->estado == 'Pendiente')
                                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                                        @elseif ($reserva->estado == 'Aceptada')
                                            <span class="text-green-600 font-semibold">Aceptada</span>
                                        @else
                                            <span class="text-red-600 font-semibold">Rechazada</span>
                                        @endif
                                    </td>

                                    {{-- Botones --}}
                                    <td class="p-2 flex gap-2">

                                        @if ($reserva->estado === 'Pendiente')

                                            {{-- ACEPTAR --}}
                                            <form action="{{ route('reservas.aceptar', $reserva) }}"
                                                  method="POST">
                                                @csrf
                                                <button class="bg-green-600 text-white px-3 py-1 rounded">
                                                    ✔ Aceptar
                                                </button>
                                            </form>

                                            {{-- RECHAZAR --}}
                                            <form action="{{ route('reservas.rechazar', $reserva) }}"
                                                  method="POST">
                                                @csrf
                                                <button class="bg-red-600 text-white px-3 py-1 rounded">
                                                    ✖ Rechazar
                                                </button>
                                            </form>

                                        @else
                                            <span class="text-gray-500">Sin acciones</span>
                                        @endif

                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @endif

        </div>

    </div>

</x-app-layout>
