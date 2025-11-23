<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buscar Rides Disponibles
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto">

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- BUSCADOR --}}
        <form method="GET" action="{{ route('public.index') }}" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label>Origen:</label>
                    <input type="text" name="origen" class="w-full border p-2 rounded">
                </div>

                <div>
                    <label>Destino:</label>
                    <input type="text" name="destino" class="w-full border p-2 rounded">
                </div>

                <div class="flex items-end">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                        🔍 Buscar
                    </button>
                </div>
            </div>
        </form>

        {{-- LISTA DE RIDES --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-semibold mb-4">Rides disponibles</h3>

            @if ($rides->isEmpty())
                <p class="text-gray-600">No hay rides con esos filtros.</p>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2">Origen</th>
                            <th class="p-2">Destino</th>
                            <th class="p-2">Fecha</th>
                            <th class="p-2">Hora</th>
                            <th class="p-2">Costo</th>
                            <th class="p-2">Espacios</th>
                            <th class="p-2">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rides as $ride)
                            <tr class="border-b">
                                <td class="p-2">{{ $ride->origen }}</td>
                                <td class="p-2">{{ $ride->destino }}</td>
                                <td class="p-2">{{ $ride->fecha }}</td>
                                <td class="p-2">{{ $ride->hora }}</td>
                                <td class="p-2">₡{{ number_format($ride->costo_por_espacio, 2) }}</td>
                                <td class="p-2">{{ $ride->espacios }}</td>
                                <td class="p-2">
                                    <form action="{{ route('reservas.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="ride_id" value="{{ $ride->id }}">
                                        <button class="bg-green-600 text-white px-3 py-1 rounded">
                                            Reservar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- HISTORIAL --}}
        <div class="mt-10 bg-white p-6 rounded shadow">
            <h3 class="text-xl font-semibold mb-4">Mis reservas</h3>

            @if ($misReservas->isEmpty())
                <p class="text-gray-600">No tienes reservas realizadas.</p>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2">Ride</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($misReservas as $res)
                            <tr class="border-b">
                                <td class="p-2">{{ $res->ride->nombre }}</td>
                                <td class="p-2">{{ $res->estado }}</td>
                                <td class="p-2">

                                    {{-- Cancelar solo si está Pendiente o Aceptada --}}
                                    @if(in_array($res->estado, ['Pendiente','Aceptada']))
                                        <form action="{{ route('reservas.cancelar', $res) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Cancelar reserva?')">

                                            @csrf
                                            <button class="text-red-600 underline">
                                                Cancelar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">Sin acciones</span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            @endif
        </div>

    </div>

</x-app-layout>
