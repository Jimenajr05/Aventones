<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buscar Rides Disponibles
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto space-y-10">

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- 🔍 FORMULARIO DE BÚSQUEDA ESTILO RIDES --}}
        <div class="bg-white p-8 rounded-2xl shadow-xl">
            <h3 class="text-2xl font-bold text-center mb-6">Buscar rides disponibles</h3>

            <form method="GET" action="{{ route('pasajero.buscar_rides') }}" class="space-y-6">

                {{-- FILA 1: ORIGEN - DESTINO --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold">Origen:</label>
                        <input type="text" name="origen" id="input-origen"
                            placeholder="Selecciona en el mapa o escribe"
                            class="w-full border p-2 rounded-lg"
                            value="{{ $origen_buscado ?? '' }}">
                    </div>

                    <div>
                        <label class="font-semibold">Destino:</label>
                        <input type="text" name="destino" id="input-destino"
                            placeholder="Selecciona en el mapa o escribe"
                            class="w-full border p-2 rounded-lg"
                            value="{{ $destino_buscado ?? '' }}">
                    </div>

                    <div>
                        <label class="font-semibold">Ordenar por:</label>
                        <select name="orden"
                                class="w-full border p-2 rounded-lg">
                            <option value="fecha" {{ ($orden_actual ?? '') === 'fecha' ? 'selected' : '' }}>Día y Hora</option>
                            <option value="precio" {{ ($orden_actual ?? '') === 'precio' ? 'selected' : '' }}>Costo</option>
                            <option value="espacios" {{ ($orden_actual ?? '') === 'espacios' ? 'selected' : '' }}>Espacios</option>
                        </select>
                    </div>
                </div>

                {{-- FILA 2: ASC/DESC - BOTONES --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="font-semibold">Dirección:</label>
                        <select name="direccion"
                                class="w-full border p-2 rounded-lg">
                            <option value="asc" {{ ($direccion_actual ?? '') === 'asc' ? 'selected' : '' }}>Ascendente</option>
                            <option value="desc" {{ ($direccion_actual ?? '') === 'desc' ? 'selected' : '' }}>Descendente</option>
                        </select>
                    </div>

                    {{-- BOTÓN BUSCAR --}}
                    <div class="flex items-end">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold shadow w-full">
                            🔍 Buscar
                        </button>
                    </div>

                    {{-- BOTÓN LIMPIAR --}}
                    <div class="flex items-end">
                        <a href="{{ route('pasajero.buscar_rides') }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow w-full text-center">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>


        {{-- 🗺️ MAPA - MISMO ESTILO DE RIDES --}}
        <div class="bg-white p-8 rounded-2xl shadow-xl space-y-4">
            
            <div id="map-hint"
                class="p-3 bg-blue-50 border-l-4 border-blue-500 text-blue-800 rounded text-center">
                🗺️ Selecciona origen y destino dentro de <b>Alajuela</b>.
            </div>

            <div id="map"
                 class="rounded-xl overflow-hidden border shadow"
                 style="height: 400px; z-index: 1;"></div>

        </div>

        {{-- 📋 LISTA DE RIDES - ESTILO RIDES --}}
        <div class="bg-white p-8 rounded-2xl shadow-xl">
            <h3 class="text-2xl font-bold text-center mb-6">Rides disponibles</h3>

            @if ($rides->isEmpty())
                <p class="text-gray-600 text-center">No hay rides con esos filtros.</p>
            @else

                {{-- ORDENAMIENTO HELPER --}}
                @php
                    function orderUrl($field, $current_order, $current_dir, $origen, $destino) {
                        $new_dir = 'asc';
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
                    $orden_actual = $orden_actual ?? 'fecha';
                    $direccion_actual = $direccion_actual ?? 'asc';
                @endphp

                {{-- TABLA --}}
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-3 text-left">Chofer</th>
                                <th class="p-3 text-left">Ride</th>
                                <th class="p-3 text-left">Vehículo</th>

                                <th class="p-3 text-left">
                                    <a href="{{ orderUrl('origen', $orden_actual, $direccion_actual, $origen_buscado, $destino_buscado) }}">
                                        Origen
                                        @if($orden_actual === 'origen') @if($direccion_actual === 'asc') ▲ @else ▼ @endif @endif
                                    </a>
                                </th>

                                <th class="p-3 text-left">
                                    <a href="{{ orderUrl('destino', $orden_actual, $direccion_actual, $origen_buscado, $destino_buscado) }}">
                                        Destino
                                        @if($orden_actual === 'destino') @if($direccion_actual === 'asc') ▲ @else ▼ @endif @endif
                                    </a>
                                </th>

                                <th class="p-3">Precio</th>

                                <th class="p-3 text-left">
                                    <a href="{{ orderUrl('fecha', $orden_actual, $direccion_actual, $origen_buscado, $destino_buscado) }}">
                                        Fecha/Hora
                                        @if($orden_actual === 'fecha') @if($direccion_actual === 'asc') ▲ @else ▼ @endif @endif
                                    </a>
                                </th>

                                <th class="p-3">Espacios</th>
                                <th class="p-3">Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($rides as $ride)
                                <tr class="border-b hover:bg-gray-50 transition">

                                    {{-- Chofer --}}
                                    <td class="p-3 text-sm">
                                        {{ $ride->user->nombre ?? 'N/A' }} {{ $ride->user->apellido ?? '' }}
                                    </td>

                                    <td class="p-3 font-semibold text-sm">{{ $ride->nombre }}</td>

                                    <td class="p-3 text-sm">
                                        @if ($ride->vehiculo)
                                            {{ $ride->vehiculo->marca }} - {{ $ride->vehiculo->modelo }}
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td class="p-3 text-sm">{{ $ride->origen }}</td>
                                    <td class="p-3 text-sm">{{ $ride->destino }}</td>

                                    <td class="p-3 text-sm whitespace-nowrap">
                                        ₡{{ number_format($ride->costo_por_espacio, 2) }}
                                    </td>

                                    <td class="p-3 text-sm whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($ride->fecha)->format('d/m/Y') }}
                                        <br>
                                        {{ \Carbon\Carbon::parse($ride->hora)->format('H:i A') }}
                                    </td>

                                    <td class="p-3 text-sm">{{ $ride->espacios }}</td>

                                    {{-- Estado / Botón reservar --}}
                                    <td class="p-3">
                                        @php
                                            $reserva = $ride->reserva_del_pasajero;
                                        @endphp

                                        {{-- Si el pasajero YA tiene una reserva en este ride --}}
                                        @if($reserva)
                                            <span class="font-semibold
                                                @if($reserva->estado == 1) text-yellow-600
                                                @elseif($reserva->estado == 2) text-green-600
                                                @elseif($reserva->estado == 3) text-red-600
                                                @else text-gray-500 @endif">
                                                {{ ['','Pendiente','Reservado','Rechazada','Cancelada'][$reserva->estado] }}
                                            </span>

                                        {{-- NUEVO: Si *otro* pasajero ya reservó este ride --}}
                                        @elseif(!empty($ride->alguien_reservo) && $ride->alguien_reservo === true)
                                            <span class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm shadow">
                                                Reservado
                                            </span>

                                        {{-- Si nadie ha reservado y aún hay espacios --}}
                                        @elseif ($ride->espacios > 0)
                                            <form action="{{ route('reservas.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="ride_id" value="{{ $ride->id }}">
                                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm shadow">
                                                    Reservar
                                                </button>
                                            </form>

                                        {{-- Lleno --}}
                                        @else
                                            <span class="text-gray-500">Lleno</span>
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


    {{-- LEAFLET --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([10.01625, -84.21163], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        let mInicio = null, mFin = null;
        let paso = "origen";
        const hint = document.getElementById("map-hint");

        const inputOrigen = document.getElementById("input-origen");
        const inputDestino = document.getElementById("input-destino");

        function esAlajuela(txt) {
            return txt.toLowerCase().includes("alajuela");
        }

        async function reverse(lat, lng) {
            const r = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
            const d = await r.json();
            return d.display_name || `${lat}, ${lng}`;
        }

        map.on("click", async e => {
            const { lat, lng } = e.latlng;
            const dir = await reverse(lat,lng);

            if (!esAlajuela(dir)) {
                hint.classList.remove("bg-blue-50","text-blue-800","border-blue-500");
                hint.classList.add("bg-red-100","text-red-700","border-red-600");
                hint.innerHTML = "❌ Solo se permiten ubicaciones en <b>Alajuela</b>";
                return;
            }

            hint.classList.remove("bg-red-100","text-red-700","border-red-600");
            hint.classList.add("bg-blue-50","text-blue-800","border-blue-500");

            if (paso === "origen") {
                if (mInicio) map.removeLayer(mInicio);
                mInicio = L.marker([lat,lng]).addTo(map).bindPopup("📍 Origen").openPopup();
                inputOrigen.value = dir;
                paso = "destino";
                hint.innerHTML = "📍 Ahora selecciona el <b>destino</b>.";
            } else {
                if (mFin) map.removeLayer(mFin);
                mFin = L.marker([lat,lng]).addTo(map).bindPopup("🏁 Destino").openPopup();
                inputDestino.value = dir;
                paso = "origen";
                hint.innerHTML = "✅ Ubicaciones listas.";
            }
        });
    </script>

</x-app-layout>
