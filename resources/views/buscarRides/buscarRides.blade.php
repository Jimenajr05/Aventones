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

        {{-- Mostrar errores de validación de reserva (si el usuario intenta reservar un ride que ya tiene) --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
                {{ $errors->first() }}
            </div>
        @endif
        
        {{-- BUSCADOR (CORREGIDO) --}}
        {{-- action apunta a la ruta de búsqueda del pasajero y usa el método GET para filtros --}}
        <form method="GET" action="{{ route('pasajero.buscar_rides') }}" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label>Origen:</label>
                    {{-- Mantiene el valor buscado --}}
                    <input type="text" name="origen" class="w-full border p-2 rounded" value="{{ $origen_buscado ?? '' }}">
                </div>

                <div>
                    <label>Destino:</label>
                    {{-- Mantiene el valor buscado --}}
                    <input type="text" name="destino" class="w-full border p-2 rounded" value="{{ $destino_buscado ?? '' }}">
                </div>

                <div class="flex items-end">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                        🔍 Buscar
                    </button>
                </div>
            </div>
            
            {{-- Campos Ocultos para el Ordenamiento. Se envían con el formulario de búsqueda. --}}
            <input type="hidden" name="orden" value="{{ $orden_actual ?? 'fecha' }}">
            <input type="hidden" name="direccion" value="{{ $direccion_actual ?? 'asc' }}">
        </form>

        {{-- LISTA DE RIDES --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-semibold mb-4">Rides disponibles</h3>

            @if ($rides->isEmpty())
                <p class="text-gray-600">No hay rides con esos filtros.</p>
            @else
                
                {{-- Helper para construir el enlace de ordenamiento --}}
                @php
                    // Esta función genera el URL de ordenamiento, conservando filtros de búsqueda.
                    function orderUrl($field, $current_order, $current_dir, $origen, $destino) {
                        $new_dir = 'asc';
                        // Si ya se está ordenando por este campo, cambiamos la dirección (asc <-> desc)
                        if ($field === ($current_order ?? 'fecha')) {
                            $new_dir = $current_dir === 'asc' ? 'desc' : 'asc';
                        }
                        return route('pasajero.buscar_rides', [
                            'origen' => $origen,
                            'destino' => $destino,
                            'orden' => $field,
                            'direccion' => $new_dir
                        ]);
                    }
                    // Valores por defecto para el helper
                    $orden_actual = $orden_actual ?? 'fecha';
                    $direccion_actual = $direccion_actual ?? 'asc';
                    $origen_buscado = $origen_buscado ?? '';
                    $destino_buscado = $destino_buscado ?? '';
                @endphp

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            {{-- ORDENAR POR ORIGEN --}}
                            <th class="p-2">
                                <a href="{{ orderUrl('origen', $orden_actual, $direccion_actual, $origen_buscado, $destino_buscado) }}">
                                    Origen 
                                    @if($orden_actual === 'origen') @if($direccion_actual === 'asc') ▲ @else ▼ @endif @endif
                                </a>
                            </th>
                            
                            {{-- ORDENAR POR DESTINO --}}
                            <th class="p-2">
                                <a href="{{ orderUrl('destino', $orden_actual, $direccion_actual, $origen_buscado, $destino_buscado) }}">
                                    Destino 
                                    @if($orden_actual === 'destino') @if($direccion_actual === 'asc') ▲ @else ▼ @endif @endif
                                </a>
                            </th>
                            
                            {{-- ORDENAR POR FECHA --}}
                            <th class="p-2">
                                <a href="{{ orderUrl('fecha', $orden_actual, $direccion_actual, $origen_buscado, $destino_buscado) }}">
                                    Fecha 
                                    @if($orden_actual === 'fecha') @if($direccion_actual === 'asc') ▲ @else ▼ @endif @endif
                                </a>
                            </th>
                            
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
                                
                                {{-- LÓGICA AÑADIDA PARA VALIDAR EL BOTÓN --}}
                                <td class="p-2">
                                    @if ($ride->reserva_del_pasajero)
                                        {{-- Ya tiene una reserva activa (Pendiente=1, Aceptada=2) o rechazada (3) --}}
                                        @if ($ride->reserva_del_pasajero->estado == 1)
                                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                                        @elseif ($ride->reserva_del_pasajero->estado == 2)
                                            <span class="text-green-600 font-semibold">Aceptada</span>
                                        @elseif ($ride->reserva_del_pasajero->estado == 3)
                                            {{-- Si está Rechazada (3), se permite reservar de nuevo --}}
                                            <form action="{{ route('reservas.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="ride_id" value="{{ $ride->id }}">
                                                <button class="bg-green-600 text-white px-3 py-1 rounded">
                                                    Reservar
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        {{-- Si NO hay reserva hecha para este ride --}}
                                        <form action="{{ route('reservas.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="ride_id" value="{{ $ride->id }}">
                                            <button class="bg-green-600 text-white px-3 py-1 rounded">
                                                Reservar
                                            </button>
                                        </form>
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