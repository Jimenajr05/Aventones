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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- 1. FORMULARIO PARA CREAR --}}
            <div class="p-6 bg-white rounded-lg shadow">
                
                <div class="max-w-lg mx-auto"> 
                
                    <h3 class="text-xl font-bold mb-4">Crear nuevo ride</h3>

                    <form action="{{ route('rides.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="font-semibold">Nombre del ride:</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                class="w-full border p-2 rounded" required>
                        </div>

                        <div>
                            <label class="font-semibold">Origen:</label>
                            <input type="text" name="origen" value="{{ old('origen') }}"
                                class="w-full border p-2 rounded" required>
                        </div>

                        <div>
                            <label class="font-semibold">Destino:</label>
                            <input type="text" name="destino" value="{{ old('destino') }}"
                                class="w-full border p-2 rounded" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-semibold">Fecha:</label>
                                <input type="date" name="fecha" value="{{ old('fecha') }}"
                                    class="w-full border p-2 rounded" required>
                            </div>
                            <div>
                                <label class="font-semibold">Hora:</label>
                                <input type="time" name="hora" value="{{ old('hora') }}"
                                    class="w-full border p-2 rounded" required>
                            </div>
                        </div>

                        
                        <div>
                            <label class="font-semibold">Vehículo:</label>
                            <select name="vehiculo_id" class="w-full border p-2 rounded" required>
                                <option value="">Seleccione un vehículo</option>
                                @foreach ($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}"
                                        {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->marca }} ({{ $vehiculo->placa }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-semibold">Costo por espacio:</label>
                                <input type="number" name="costo_por_espacio" value="{{ old('costo_por_espacio') }}"
                                    step="0.01" class="w-full border p-2 rounded" required>
                            </div>
                            <div>
                                <label class="font-semibold">Espacios disponibles:</label>
                                <input type="number" name="espacios" value="{{ old('espacios') }}"
                                    class="w-full border p-2 rounded" required min="1">
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-4 w-full bg-indigo-600 text-white p-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                            Publicar Ride
                        </button>
                    </form>
                    
                </div>
            </div>


            {{-- 2. Listado de Rides --}}
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4">Mis Rides Publicados ({{ $rides->count() }})</h3>
                
                @if ($rides->isEmpty())
                    <p class="text-gray-600">Aún no has publicado ningún ride. Usa el formulario de la izquierda.</p>
                @endif
                
                <div class="grid grid-cols-1 gap-4 mt-6">
                    @foreach ($rides as $ride)
                        
                        @php
                            // Filtramos solo las reservas activas (Pendientes: 1 y Aceptadas: 2)
                            $activeReservas = $ride->reservas->whereIn('estado', [1, 2]);
                            $activeReservasCount = $activeReservas->count();
                            $espaciosReservados = $activeReservas->sum('espacios_reservados');
                        @endphp
                        
                        <div class="p-4 bg-white rounded-lg shadow border border-gray-100 space-y-2">
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
                            
                            {{-- SECCIÓN DE BOTONES DE ACCIÓN --}}
                            <div class="flex gap-2 mt-4">

                                @if ($activeReservasCount > 0)
                                    
                                    <span class="inline-block py-1 px-3 text-sm rounded-full text-gray-700 font-semibold bg-gray-200">
                                        🔒 No se puede editar (Reservado)
                                    </span>
                                    
                                    {{-- El botón de eliminar se mantiene, el controlador validará el bloqueo --}}
                                    <form action="{{ route('rides.destroy', $ride->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este ride? Si tiene reservas activas, el sistema te lo impedirá.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-block py-1 px-3 text-sm rounded-full text-white font-semibold 
                                                    bg-red-600 transition duration-300 hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </form>
                                @else
                                    {{-- Botón de Editar (solo si NO tiene reservas activas) --}}
                                    <button onclick="openEditModal({{ $ride->toJson() }})"
                                        class="inline-block py-1 px-3 text-sm rounded-full text-white font-semibold 
                                                bg-blue-600 transition duration-300 hover:bg-blue-700">
                                        ✏️ Editar
                                    </button>

                                    {{-- Botón de Eliminar (solo si NO tiene reservas activas) --}}
                                    <form action="{{ route('rides.destroy', $ride->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este ride? Esta acción es irreversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-block py-1 px-3 text-sm rounded-full text-white font-semibold 
                                                    bg-red-600 transition duration-300 hover:bg-red-700">
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


    <div id="editRideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 items-center justify-center">
        
        {{-- **CLAVE:** max-w-lg para hacerlo menos ancho que max-w-xl --}}
        <div class="bg-white p-6 rounded-lg w-full max-w-xl"> 
            
            <h3 class="text-xl font-bold mb-4">Editar Ride</h3>

            <form id="editRideForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PATCH') 
                
                {{-- Campos del formulario de edición --}}
                <div>
                    <label class="font-semibold">Nombre del ride:</label>
                    <input type="text" id="edit_nombre" name="nombre" class="w-full border p-2 rounded" required>
                </div>
                
                {{-- Origen y Destino ahora en la misma fila --}}
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

                {{-- Fecha y Hora ahora en la misma fila --}}
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
                        <option value="">Seleccione un vehículo</option>
                        @foreach ($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}">
                                {{ $vehiculo->marca }} ({{ $vehiculo->placa }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Costo y Espacios ahora en la misma fila --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Costo por espacio:</label>
                        <input type="number" id="edit_costo_por_espacio" name="costo_por_espacio" step="0.01" class="w-full border p-2 rounded" required>
                    </div>
                    <div>
                        <label class="font-semibold">Espacios disponibles:</label>
                        <input type="number" id="edit_espacios" name="espacios" class="w-full border p-2 rounded" required min="1">
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


    <script>
        function openEditModal(ride) {
            // Rellena los campos del modal
            document.getElementById('edit_nombre').value = ride.nombre;
            document.getElementById('edit_origen').value = ride.origen;
            document.getElementById('edit_destino').value = ride.destino;
            document.getElementById('edit_fecha').value = ride.fecha;

            // **CORRECCIÓN CLAVE para la hora:** elimina los segundos (HH:MM:SS -> HH:MM)
            if (ride.hora && ride.hora.length > 5) {
                document.getElementById("edit_hora").value = ride.hora.substring(0, 5);
            } else {
                document.getElementById("edit_hora").value = ride.hora;
            }

            document.getElementById('edit_costo_por_espacio').value = ride.costo_por_espacio;
            document.getElementById('edit_espacios').value = ride.espacios;
            document.getElementById('edit_vehiculo_id').value = ride.vehiculo_id;

            // Establece la acción del formulario
            document.getElementById('editRideForm').action = '/rides/' + ride.id;

            // Muestra el modal
            document.getElementById('editRideModal').classList.remove('hidden');
            document.getElementById('editRideModal').classList.add('flex');
        }

        function closeEditModal() {
            // Oculta el modal
            document.getElementById('editRideModal').classList.add('hidden');
            document.getElementById('editRideModal').classList.remove('flex');
        }
    </script>

</x-app-layout>
