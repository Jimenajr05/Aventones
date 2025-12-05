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
                            {{-- AÑADIDO: id=vehiculo_id_create para JS y data-capacidad --}}
                            <select name="vehiculo_id" id="vehiculo_id_create" class="w-full border p-2 rounded" required>
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

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-semibold">Costo por espacio:</label>
                                <input type="number" name="costo_por_espacio" value="{{ old('costo_por_espacio') }}"
                                    step="0.01" class="w-full border p-2 rounded" required>
                            </div>
                            <div>
                                {{-- MODIFICADO: Muestra el máximo de espacios y añade id=espacios_create y max --}}
                                <label class="font-semibold">Espacios disponibles (Máx: <span id="max_espacios_display">N/A</span>):</label>
                                <input type="number" name="espacios" id="espacios_create" value="{{ old('espacios') }}"
                                    class="w-full border p-2 rounded" required min="1" max="1">
                                <small class="text-gray-500 block mt-1">Capacidad máxima: Capacidad del vehículo - 1 (chofer).</small>
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
                        
                        {{-- CONTENEDOR DE LA TARJETA DEL RIDE --}}
                        <div class="p-4 bg-white rounded-lg shadow border border-gray-100 flex items-start space-x-4">
                            
                            <div class="flex-shrink-0">
                                @if ($ride->vehiculo && $ride->vehiculo->fotografia)
                                    <img src="{{ asset('storage/'.$ride->vehiculo->fotografia) }}"
                                         class="w-24 h-24 object-cover rounded-md">
                                @else
                                    {{-- Placeholder si no hay foto --}}
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
                            
                            {{-- SECCIÓN DE BOTONES DE ACCIÓN (Corregido para alinear a la derecha, quitando ml-auto) --}}
                            <div class="flex-shrink-0 flex flex-col space-y-4"> {{-- ELIMINADA CLASE ml-auto --}}

                                @if ($activeReservasCount > 0)
                                    
                                    <span class="inline-block py-2 px-4 text-sm rounded-full text-gray-700 font-semibold bg-gray-200 text-center whitespace-nowrap">
                                        🔒 Reservado
                                    </span>
                                    
                                    {{-- El botón de eliminar se mantiene, adaptado al nuevo estilo --}}
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
                                    {{-- Botón de Editar --}}
                                    <button onclick="openEditModal({{ $ride->toJson() }})"
                                        class="inline-block py-2 px-4 text-sm rounded-full text-white font-semibold 
                                                bg-blue-600 transition duration-300 hover:bg-blue-700 whitespace-nowrap">
                                        ✏️ Editar
                                    </button>

                                    {{-- Botón de Eliminar --}}
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


    <div id="editRideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 items-center justify-center">
        
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
                    {{-- AÑADIDO: id=edit_vehiculo_id para JS --}}
                    <select id="edit_vehiculo_id" name="vehiculo_id" class="w-full border p-2 rounded" required>
                        <option value="" data-capacidad="0">Seleccione un vehículo</option>
                        @foreach ($vehiculos as $vehiculo)
                            {{-- AÑADIDO data-capacidad --}}
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
                        {{-- MODIFICADO: Muestra el máximo de espacios y añade id=edit_espacios y max --}}
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


    <script>
        // Función general para calcular y aplicar el límite de espacios
        function updateMaxSpaces(selectId, inputId, maxDisplayId) {
            const selectVehiculo = document.getElementById(selectId);
            const inputEspacios = document.getElementById(inputId);
            const maxDisplay = document.getElementById(maxDisplayId);

            const selectedOption = selectVehiculo.options[selectVehiculo.selectedIndex];
            const capacidadTotal = parseInt(selectedOption.getAttribute('data-capacidad') || 0);
            
            // Capacidad máxima de pasajeros = Capacidad Total - 1 (Chofer)
            const maxEspacios = capacidadTotal > 0 ? capacidadTotal - 1 : 0;
            
            // Aplicar el nuevo máximo al input
            inputEspacios.setAttribute('max', maxEspacios);
            maxDisplay.textContent = maxEspacios;

            // Asegurar que el valor actual no exceda el nuevo máximo
            if (parseInt(inputEspacios.value) > maxEspacios || parseInt(inputEspacios.value) < 1) {
                // Si la capacidad es 0, el valor debe ser 0
                if (maxEspacios === 0) {
                    inputEspacios.value = 0;
                } else {
                    // Si el valor actual es inválido, usar el valor máximo
                    inputEspacios.value = maxEspacios;
                }
            }
            // Si la capacidad máxima es 0 (ej. un vehículo para 1), deshabilitar la entrada y poner 0
            if (maxEspacios === 0) {
                 inputEspacios.value = 0;
                 inputEspacios.disabled = true;
            } else {
                 inputEspacios.disabled = false;
            }
        }

        // Script de inicialización para el formulario de CREACIÓN
        document.addEventListener('DOMContentLoaded', function () {
            const selectVehiculoCreate = document.getElementById('vehiculo_id_create');

            // 1. Inicializar la capacidad al cargar
            updateMaxSpaces('vehiculo_id_create', 'espacios_create', 'max_espacios_display');

            // 2. Escuchar el cambio en la selección de vehículo
            selectVehiculoCreate.addEventListener('change', function() {
                updateMaxSpaces('vehiculo_id_create', 'espacios_create', 'max_espacios_display');
            });
        });
        
        // Modal de Edición
        function openEditModal(ride) {
            // Rellena los campos del modal
            document.getElementById('edit_nombre').value = ride.nombre;
            document.getElementById('edit_origen').value = ride.origen;
            document.getElementById('edit_destino').value = ride.destino;
            document.getElementById('edit_fecha').value = ride.fecha;

            // CORRECCIÓN CLAVE para la hora: elimina los segundos (HH:MM:SS -> HH:MM)
            if (ride.hora && ride.hora.length > 5) {
                document.getElementById("edit_hora").value = ride.hora.substring(0, 5);
            } else {
                document.getElementById("edit_hora").value = ride.hora;
            }

            document.getElementById('edit_costo_por_espacio').value = ride.costo_por_espacio;
            document.getElementById('edit_vehiculo_id').value = ride.vehiculo_id;
            
            // 💡 EJECUTA la función de límite antes de asignar el valor de espacios
            // Esto asegura que el campo tenga el MAX correcto para el vehículo
            updateMaxSpaces('edit_vehiculo_id', 'edit_espacios', 'edit_max_espacios_display');

            // Asigna el valor del ride DESPUÉS de establecer el MAX
            // Si el valor del ride es mayor que el nuevo MAX, el JS ya lo habrá corregido
            document.getElementById('edit_espacios').value = ride.espacios;


            // Vuelve a aplicar la lógica si cambia el vehículo en el modal
            document.getElementById('edit_vehiculo_id').onchange = function() {
                 updateMaxSpaces('edit_vehiculo_id', 'edit_espacios', 'edit_max_espacios_display');
            };

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