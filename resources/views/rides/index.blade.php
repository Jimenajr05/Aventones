<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Rides
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
                <strong>Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        {{-- TODO APILADO COMO EN EL DISEÑO --}}
        <div class="space-y-10">

            {{-- 1. FORMULARIO PARA CREAR --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">

                <h3 class="text-2xl font-bold text-center mb-6">Crear nuevo ride</h3>

                <div class="max-w-4xl mx-auto">

                    <form action="{{ route('rides.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- FILA 1: Nombre, Origen, Destino, Hora --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="font-semibold">Nombre del ride:</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                    class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                <label class="font-semibold">Origen:</label>
                                <input type="text" name="origen" id="input-origen" value="{{ old('origen') }}"
                                    class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                <label class="font-semibold">Destino:</label>
                                <input type="text" name="destino" id="input-destino" value="{{ old('destino') }}"
                                    class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                <label class="font-semibold">Hora:</label>
                                <input type="time" name="hora" value="{{ old('hora') }}"
                                    class="w-full border p-2 rounded-lg" required>
                            </div>
                        </div>

                        {{-- FILA 2: Fecha, Vehículo, Costo, Espacios --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="font-semibold">Fecha:</label>
                                <input type="date" name="fecha" value="{{ old('fecha') }}"
                                    class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                <label class="font-semibold">Vehículo:</label>
                                {{-- AÑADIDO: id=vehiculo_id_create para JS y data-capacidad --}}
                                <select name="vehiculo_id" id="vehiculo_id_create" class="w-full border p-2 rounded-lg"
                                        required>
                                    <option value="" data-capacidad="0">Seleccione un vehículo</option>
                                    @foreach ($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}"
                                            data-capacidad="{{ $vehiculo->capacidad }}"
                                            {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                            {{ $vehiculo->marca }} ({{ $vehiculo->placa }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="font-semibold">Costo por espacio:</label>
                                <input type="number" name="costo_por_espacio" value="{{ old('costo_por_espacio') }}"
                                    step="0.01" class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                {{-- MODIFICADO: Muestra el máximo de espacios y añade id=espacios_create y max --}}
                                <label class="font-semibold">
                                    Espacios (Máx: <span id="max_espacios_display">N/A</span>):
                                </label>
                                <input type="number" name="espacios" id="espacios_create" value="{{ old('espacios') }}"
                                    class="w-full border p-2 rounded-lg" required min="1" max="1">
                                <small class="text-gray-500 block mt-1">
                                    Capacidad máxima: Capacidad del vehículo - 1 (chofer).
                                </small>
                            </div>
                        </div>

                        {{-- 🗺️ MAPA AGREGADO AQUI --}}
                        <div id="map-hint"
                             class="mt-2 mb-4 p-3 bg-blue-50 border-l-4 border-blue-500 text-blue-800 rounded text-center">
                            🗺️ Selecciona en el mapa primero el <b>origen</b> y luego el <b>destino</b>.
                        </div>

                        <div id="map" class="rounded-xl overflow-hidden border" style="height: 350px; z-index:1;"></div>
                        {{-- FIN MAPA --}}

                        <div class="mt-4 flex justify-center">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 shadow">
                                Publicar Ride
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            {{-- 2. Listado de Rides --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold text-center mb-6">
                    Mis Rides Publicados ({{ $rides->count() }})
                </h3>
                
                @if ($rides->isEmpty())
                    <p class="text-gray-600 text-center">Aún no has publicado ningún ride. Usa el formulario de arriba.</p>
                @endif
                
                <div class="grid grid-cols-1 gap-4 mt-6">
                    @foreach ($rides as $ride)
                        
                        @php
                            // Filtramos solo las reservas activas (Pendientes: 1 y Aceptadas: 2)
                            $activeReservas = $ride->reservas->whereIn('estado', [1, 2]);
                            $activeReservasCount = $activeReservas->count();
                            $espaciosReservados = $activeReservas->sum('espacios_reservados');
                        @endphp
                        
                        {{-- CONTENEDOR --}}
                        <div class="p-4 bg-white rounded-lg shadow border border-gray-100 flex items-start space-x-4">
                            
                            <div class="flex-shrink-0">
                                @if ($ride->vehiculo && $ride->vehiculo->fotografia)
                                    <img src="{{ asset('storage/'.$ride->vehiculo->fotografia) }}"
                                         class="w-24 h-24 object-cover rounded-md">
                                @else
                                    <div class="w-24 h-24 bg-gray-100 rounded-md flex items-center justify-center text-gray-500 text-xs text-center">
                                        <p>Sin foto de auto</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-grow space-y-1">
                                <h4 class="text-lg font-bold text-gray-800">{{ $ride->nombre }}</h4>
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Ruta:</span> {{ $ride->origen }} → {{ $ride->destino }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Fecha:</span> {{ \Carbon\Carbon::parse($ride->fecha)->format('d/m/Y') }} 
                                    | <span class="font-semibold">Hora:</span> {{ \Carbon\Carbon::parse($ride->hora)->format('H:i') }}
                                </p>
                                
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Costo:</span> ₡{{ number_format($ride->costo_por_espacio, 2) }}
                                    | <span class="font-semibold">Espacios:</span> 
                                    {{ $ride->espacios - $espaciosReservados }} / {{ $ride->espacios }}
                                </p>
                            </div>
                            
                            <div class="flex-shrink-0 flex flex-col space-y-4">

                                @if ($activeReservasCount > 0)
                                    
                                    <span class="inline-block py-2 px-4 text-sm rounded-full text-gray-700 font-semibold bg-gray-200 text-center whitespace-nowrap">
                                        🔒 Reservado
                                    </span>
                                    
                                    <form action="{{ route('rides.destroy', $ride->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este ride? Si tiene reservas activas, el sistema te lo impedirá.');"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-block py-2 px-4 text-sm rounded-full text-white font-semibold 
                                                    bg-red-600 transition duration-300 hover:bg-red-700 whitespace-nowrap">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                @else
                                    <button onclick="openEditModal({{ $ride->toJson() }})"
                                        class="inline-block py-2 px-4 text-sm rounded-full text-white font-semibold 
                                                bg-blue-600 transition duration-300 hover:bg-blue-700 whitespace-nowrap">
                                        ✏️ Editar
                                    </button>

                                    <form action="{{ route('rides.destroy', $ride->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este ride? Esta acción es irreversible.');"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-block py-2 px-4 text-sm rounded-full text-white font-semibold 
                                                    bg-red-600 transition duration-300 hover:bg-red-700 whitespace-nowrap">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                @endif

                            </div>
                            
                        </div>
                    @endforeach
                </div>
                
            </div>

        </div>

    </div>


    {{-- MODAL --}}
    <div id="editRideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 items-center justify-center">
        
        <div class="bg-white p-6 rounded-lg w-full max-w-xl"> 
            
            <h3 class="text-xl font-bold mb-4">Editar Ride</h3>

            <form id="editRideForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PATCH') 
                
                <div>
                    <label class="font-semibold">Nombre del ride:</label>
                    <input type="text" id="edit_nombre" name="nombre" class="w-full border p-2 rounded" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Origen:</label>
                        <input type="text" id="edit_origen" name="origen" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Destino:</label>
                        <input type="text" id="edit_destino" name="destino" class="w-full border p-2 rounded" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Fecha:</label>
                        <input type="date" id="edit_fecha" name="fecha" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Hora:</label>
                        <input type="time" id="edit_hora" name="hora" class="w-full border p-2 rounded" required>
                    </div>
                </div>

    
                <div>
                    <label class="font-semibold">Vehículo:</label>
                    <select id="edit_vehiculo_id" name="vehiculo_id" class="w-full border p-2 rounded" required>
                        <option value="" data-capacidad="0">Seleccione un vehículo</option>
                        @foreach ($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" data-capacidad="{{ $vehiculo->capacidad }}">
                                {{ $vehiculo->marca }} ({{ $vehiculo->placa }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Costo por espacio:</label>
                        <input type="number" id="edit_costo_por_espacio" name="costo_por_espacio" step="0.01" class="w-full border p-2 rounded" required>
                    </div>
                    <div>
                        <label class="font-semibold">Espacios disponibles (Máx: <span id="edit_max_espacios_display">N/A</span>):</label>
                        <input type="number" id="edit_espacios" name="espacios" class="w-full border p-2 rounded" required min="1" max="1">
                        <small class="text-gray-500 block mt-1">Capacidad máxima: Capacidad del vehículo - 1 (chofer).</small>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-300 text-gray-800 py-2 px-4 rounded-lg font-semibold hover:bg-gray-400 transition duration-300">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                        Guardar Cambios
                    </button>
                </div>

            </form>

        </div>

    </div>


    {{-- ---------- SCRIPTS EXISTENTES ---------- --}}
    <script>
        function updateMaxSpaces(selectId, inputId, maxDisplayId) {
            const selectVehiculo = document.getElementById(selectId);
            const inputEspacios = document.getElementById(inputId);
            const maxDisplay = document.getElementById(maxDisplayId);

            const selectedOption = selectVehiculo.options[selectVehiculo.selectedIndex];
            const capacidadTotal = parseInt(selectedOption.getAttribute('data-capacidad') || 0);
            
            const maxEspacios = capacidadTotal > 0 ? capacidadTotal - 1 : 0;
            
            inputEspacios.setAttribute('max', maxEspacios);
            maxDisplay.textContent = maxEspacios;

            if (parseInt(inputEspacios.value) > maxEspacios || parseInt(inputEspacios.value) < 1) {
                if (maxEspacios === 0) {
                    inputEspacios.value = 0;
                } else {
                    inputEspacios.value = maxEspacios;
                }
            }
            if (maxEspacios === 0) {
                 inputEspacios.value = 0;
                 inputEspacios.disabled = true;
            } else {
                 inputEspacios.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const selectVehiculoCreate = document.getElementById('vehiculo_id_create');

            updateMaxSpaces('vehiculo_id_create', 'espacios_create', 'max_espacios_display');

            selectVehiculoCreate.addEventListener('change', function() {
                updateMaxSpaces('vehiculo_id_create', 'espacios_create', 'max_espacios_display');
            });
        });
        
        function openEditModal(ride) {

            document.getElementById('edit_nombre').value = ride.nombre;
            document.getElementById('edit_origen').value = ride.origen;
            document.getElementById('edit_destino').value = ride.destino;
            document.getElementById('edit_fecha').value = ride.fecha;

            if (ride.hora && ride.hora.length > 5) {
                document.getElementById("edit_hora").value = ride.hora.substring(0, 5);
            } else {
                document.getElementById("edit_hora").value = ride.hora;
            }

            document.getElementById('edit_costo_por_espacio').value = ride.costo_por_espacio;
            document.getElementById('edit_vehiculo_id').value = ride.vehiculo_id;
            
            updateMaxSpaces('edit_vehiculo_id', 'edit_espacios', 'edit_max_espacios_display');

            document.getElementById('edit_espacios').value = ride.espacios;

            document.getElementById('edit_vehiculo_id').onchange = function() {
                 updateMaxSpaces('edit_vehiculo_id', 'edit_espacios', 'edit_max_espacios_display');
            };

            document.getElementById('editRideForm').action = '/rides/' + ride.id;

            document.getElementById('editRideModal').classList.remove('hidden');
            document.getElementById('editRideModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editRideModal').classList.add('hidden');
            document.getElementById('editRideModal').classList.remove('flex');
        }
    </script>


    {{-- ---------- LEAFLET MAPA AGREGADO ---------- --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([10.01625, -84.21163], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

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
            const dir = await reverse(lat, lng);

            if (!esAlajuela(dir)) {
                hint.classList.remove("text-blue-800", "border-blue-500", "bg-blue-50");
                hint.classList.add("text-red-700", "border-red-600", "bg-red-100");
                hint.innerHTML = "❌ Solo se permiten ubicaciones dentro de <b>Alajuela</b>";
                return;
            }

            hint.classList.remove("text-red-700", "border-red-600", "bg-red-100");
            hint.classList.add("text-blue-800", "border-blue-500", "bg-blue-50");

            if (paso === "origen") {
                if (mInicio) map.removeLayer(mInicio);
                mInicio = L.marker([lat, lng]).addTo(map).bindPopup("📍 Origen").openPopup();

                inputOrigen.value = dir;

                paso = "destino";
                hint.innerHTML = "📍 Ahora selecciona el <b>destino</b>.";
            } else {
                if (mFin) map.removeLayer(mFin);
                mFin = L.marker([lat, lng]).addTo(map).bindPopup("🏁 Destino").openPopup();

                inputDestino.value = dir;

                paso = "origen";
                hint.innerHTML = "✅ Ubicaciones listas.";
            }
        });
    </script>

</x-app-layout>
