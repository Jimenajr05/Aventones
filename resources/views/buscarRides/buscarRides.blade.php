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
                    {{-- Mantiene el valor buscado. Añadido ID para JS --}}
                    <input type="text" name="origen" id="input-origen" class="w-full border p-2 rounded" value="{{ $origen_buscado ?? '' }}">
                </div>

                <div>
                    <label>Destino:</label>
                    {{-- Mantiene el valor buscado. Añadido ID para JS --}}
                    <input type="text" name="destino" id="input-destino" class="w-full border p-2 rounded" value="{{ $destino_buscado ?? '' }}">
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

        {{-- ESTRUCTURA DEL MAPA (AÑADIDO) --}}
        {{-- Tendrías que definir un estilo para #map en tu CSS, por ejemplo: height: 400px; z-index: 10; --}}
        <div id="map-hint" class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-500 text-blue-800 rounded">
            🗺️ Selecciona en el mapa <b>origen</b> y <b>destino</b> dentro de Alajuela.
        </div>
        {{-- El mapa necesita altura para verse. Sugiero un estilo inline temporal o añadir a tu CSS global --}}
        <div id="map" style="height: 400px; margin-bottom: 24px; z-index: 1;"></div>
        {{-- FIN ESTRUCTURA MAPA --}}

        {{-- LISTA DE RIDES --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-semibold mb-4">Rides disponibles</h3>

            @if ($rides->isEmpty())
                <p class="text-gray-600">No hay rides con esos filtros.</p>
            @else
                
                {{-- Helper para construir el enlace de ordenamiento... (código omitido por brevedad, es el mismo) --}}
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
                                
                                {{-- LÓGICA AÑADIDA PARA VALIDAR EL BOTÓN... (código omitido por brevedad, es el mismo) --}}
                                <td class="p-2">
                                    @if ($ride->reserva_del_pasajero)
                                        {{-- Ya tiene una reserva activa (Pendiente=1, Aceptada=2) o rechazada (3) --}}
                                        @if ($ride->reserva_del_pasajero->estado == 1)
                                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                                        @elseif ($ride->reserva_del_pasajero->estado == 2)
                                            {{-- CAMBIO REALIZADO AQUÍ: Dice "Reservado" en lugar de "Aceptada" --}}
                                            <span class="text-green-600 font-semibold">Reservado</span>
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

    {{-- SCRIPTS Y ESTILOS DE LEAFLET (AÑADIDO) --}}
    {{-- Debes asegurarte de tener el CSS y JS de Leaflet. Aquí se incluyen los enlaces directos. --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Lógica del mapa adaptada de index.blade.php
        const map = L.map('map').setView([10.01625, -84.21163], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        let mInicio = null, mFin = null;
        let paso = "origen";
        const hint = document.getElementById("map-hint");
        
        // Elementos input del formulario
        const inputOrigen = document.getElementById("input-origen");
        const inputDestino = document.getElementById("input-destino");


        function esAlajuela(txt) {
            // Asumiendo que la validación de ubicación sigue siendo solo en Alajuela.
            return txt.toLowerCase().includes("alajuela");
        }

        async function reverse(lat, lng) {
            const r = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
            const d = await r.json();
            return d.display_name || `${lat}, ${lng}`;
        }

        map.on("click", async e => {
            const { lat, lng } = e.latlng;
            const dir = await reverse(lat, lng);

            if (!esAlajuela(dir)) {
                // Para que el color rojo funcione, deberías tener la clase 'map-error' en tu CSS. 
                // Aquí usamos clases de Tailwind que están en el archivo 'index.blade.php' (sin verlas, hago una aproximación).
                hint.classList.remove("text-blue-800", "border-blue-500", "bg-blue-50");
                hint.classList.add("text-red-700", "border-red-600", "bg-red-100");
                hint.innerHTML = "❌ Solo se permiten ubicaciones dentro de <b>Alajuela</b>";
                return;
            } else {
                hint.classList.remove("text-red-700", "border-red-600", "bg-red-100");
                hint.classList.add("text-blue-800", "border-blue-500", "bg-blue-50");
            }

            if (paso === "origen") {
                if (mInicio) map.removeLayer(mInicio);
                mInicio = L.marker([lat, lng]).addTo(map).bindPopup("📍 Origen").openPopup();
                inputOrigen.value = dir; // Usar el input con ID
                paso = "destino";
                hint.innerHTML = "📍 Ahora selecciona el <b>destino</b>.";
            } else {
                if (mFin) map.removeLayer(mFin);
                mFin = L.marker([lat, lng]).addTo(map).bindPopup("🏁 Destino").openPopup();
                inputDestino.value = dir; // Usar el input con ID
                paso = "origen";
                hint.innerHTML = "✅ Ubicaciones listas. Haz clic en buscar.";
            }
        });
    </script>
    {{-- FIN SCRIPTS Y ESTILOS DE LEAFLET --}}

</x-app-layout>