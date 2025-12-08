<x-app-layout>
    
    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">

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
    
        {{-- Mis reservas (Pendientes y Aceptadas) --}}
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-semibold mb-4">Mis reservas</h3>

            @if ($misReservas->isEmpty())
                <p class="text-gray-600">No tienes reservas activas (Pendientes o Aceptadas).</p>
            @else
                <div class="max-w-5xl mx-auto"> 
                    <div class="overflow-x-auto">
                        {{-- TABLA --}}
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="p-2 text-left">Chofer</th>
                                    <th class="p-2 text-left">Ride</th>
                                    <th class="p-2 text-left">Vehículo</th>
                                    <th class="p-2 text-left">Origen</th>
                                    <th class="p-2 text-left">Destino</th>
                                    <th class="p-2 text-left">Precio</th>
                                    <th class="p-2 text-left">Fecha/Hora</th>
                                    <th class="p-2 text-left">Espacios</th>
                                    <th class="p-2 text-left">Estado</th>
                                    <th class="p-2 text-left">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($misReservas as $res)
                                    <tr class="border-b">
                                        {{-- Chofer --}}
                                        <td class="p-2 whitespace-nowrap">
                                            {{ $res->ride->user->nombre ?? 'N/A' }} {{ $res->ride->user->apellido ?? '' }}
                                        </td>
                                        
                                        {{-- Nombre del Ride --}}
                                        <td class="p-2">{{ $res->ride->nombre }}</td>

                                        {{-- Vehículo --}}
                                        <td class="p-2 whitespace-nowrap">
                                            {{ $res->ride->vehiculo->marca ?? 'N/A' }} - 
                                            {{ $res->ride->vehiculo->modelo ?? '' }}
                                        </td>

                                        {{-- Origen --}}
                                        <td class="p-2">{{ $res->ride->origen }}</td>

                                        {{-- Destino --}}
                                        <td class="p-2">{{ $res->ride->destino }}</td>

                                        {{-- Precio --}}
                                        <td class="p-2 whitespace-nowrap">
                                            ₡{{ number_format($res->ride->costo_por_espacio, 2) }}
                                        </td>

                                        {{-- Fecha/Hora --}}
                                        <td class="p-2 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($res->ride->fecha)->format('d/m/Y') }}
                                            <br>
                                            {{ \Carbon\Carbon::parse($res->ride->hora)->format('h:i A') }}
                                        </td>

                                        {{-- Espacios --}}
                                        <td class="p-2">
                                            {{ $res->espacios ?? 1 }}
                                        </td>

                                        {{-- ESTADO: Usando valores numéricos --}}
                                        <td class="p-2 whitespace-nowrap">
                                            @if ($res->estado == 1) 
                                                <span class="text-yellow-600 font-semibold">Pendiente</span>
                                            @elseif ($res->estado == 2)
                                                <span class="text-green-600 font-semibold">Aceptada</span>
                                            @endif
                                        </td>
                                        
                                        {{-- ACCIONES --}}
                                        <td class="p-2">
                                            {{-- Permitir CANCELAR si está Pendiente (1) o Aceptada (2) --}}
                                            <form action="{{ route('reservas.cancelar', $res) }}" 
                                                    method="POST" 
                                                    onsubmit="return confirm('¿Está seguro de que desea cancelar esta reserva?')">

                                                @csrf
                                                <button class="bg-red-600 text-white px-3 py-1 rounded">
                                                    Cancelar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            @endif
        </div>
        
        {{-- Historial de reservas (Rechazadas y Canceladas) --}}
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-semibold mb-4">Historial de reservas</h3>

            @if ($historialReservas->isEmpty())
                <p class="text-gray-600">No hay reservas en el historial (Rechazadas o Canceladas).</p>
            @else
                <div class="max-w-5xl mx-auto">
                    <div class="overflow-x-auto">
                        {{-- TABLA --}}
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="p-2 text-left">Chofer</th>
                                    <th class="p-2 text-left">Ride</th>
                                    <th class="p-2 text-left">Vehículo</th>
                                    <th class="p-2 text-left">Origen</th>
                                    <th class="p-2 text-left">Destino</th>
                                    <th class="p-2 text-left">Precio</th>
                                    <th class="p-2 text-left">Fecha/Hora</th>
                                    <th class="p-2 text-left">Espacios</th>
                                    <th class="p-2 text-left">Estado</th>
                                    <th class="p-2 text-left">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($historialReservas as $res)
                                    <tr class="border-b">
                                        {{-- Chofer --}}
                                        <td class="p-2 whitespace-nowrap">
                                            {{ $res->ride->user->nombre ?? 'N/A' }} {{ $res->ride->user->apellido ?? '' }}
                                        </td>
                                        
                                        {{-- Nombre del Ride --}}
                                        <td class="p-2">{{ $res->ride->nombre }}</td>

                                        {{-- Vehículo --}}
                                        <td class="p-2 whitespace-nowrap">
                                            {{ $res->ride->vehiculo->marca ?? 'N/A' }} - 
                                            {{ $res->ride->vehiculo->modelo ?? '' }}
                                        </td>

                                        {{-- Origen --}}
                                        <td class="p-2">{{ $res->ride->origen }}</td>

                                        {{-- Destino --}}
                                        <td class="p-2">{{ $res->ride->destino }}</td>

                                        {{-- Precio --}}
                                        <td class="p-2 whitespace-nowrap">
                                            ₡{{ number_format($res->ride->costo_por_espacio, 2) }}
                                        </td>

                                        {{-- Fecha/Hora --}}
                                        <td class="p-2 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($res->ride->fecha)->format('d/m/Y') }}
                                            <br>
                                            {{ \Carbon\Carbon::parse($res->ride->hora)->format('h:i A') }}
                                        </td>

                                        {{-- Espacios --}}
                                        <td class="p-2">
                                            {{ $res->espacios ?? 1 }}
                                        </td>

                                        {{-- Estados --}}
                                        <td class="p-2 whitespace-nowrap">
                                            @if ($res->estado == 3)
                                                <span class="text-red-600 font-semibold">Rechazada</span>
                                            @elseif ($res->estado == 4)
                                                <span class="text-gray-500 font-semibold">Cancelada</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Acciones --}}
                                        <td class="p-2">
                                            <span class="text-gray-400">Sin acciones</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    </div>

</x-app-layout>
