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

            {{-- FORMULARIO PARA CREAR --}}
            <div class="p-6 bg-white rounded-lg shadow">

                <h3 class="text-xl font-bold mb-4">Crear nuevo ride</h3>

                {{-- Aquí mantenemos solo la lógica de CREACIÓN --}}
                <form action="{{ route('rides.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="font-semibold">Nombre del ride:</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                            class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Lugar de salida:</label>
                        <input type="text" name="origen" value="{{ old('origen') }}"
                            class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Lugar de llegada:</label>
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

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold">Costo por espacio (₡):</label>
                            <input type="number" step="0.01" name="costo_por_espacio" value="{{ old('costo_por_espacio') }}"
                                   class="w-full border p-2 rounded" required>
                        </div>

                        <div>
                            <label class="font-semibold">Cantidad de espacios:</label>
                            {{-- CAMBIO APLICADO: Se quita el valor por defecto ', 4' de old() --}}
                            <input type="number" name="espacios" min="1" max="5" value="{{ old('espacios') }}"
                                   class="w-full border p-2 rounded" required>
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold">Vehículo asociado:</label>
                        <select name="vehiculo_id" class="w-full border p-2 rounded" required>
                            <option value="">Seleccione un vehículo...</option>
                            @foreach ($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->id }}"
                                    {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                            🚙 Crear ride
                        </button>
                    </div>

                </form>
            </div>

            {{-- TABLA DE RIDES --}}
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4">Mis Rides</h3>

                @if ($rides->isEmpty())
                    <p class="text-gray-500">No tienes rides registrados.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-200 text-left">
                                    <th class="p-2">Nombre</th>
                                    <th class="p-2">Origen</th>
                                    <th class="p-2">Destino</th>
                                    <th class="p-2">Fecha</th>
                                    <th class="p-2">Hora</th>
                                    <th class="p-2">Vehículo</th>
                                    <th class="p-2">Costo</th>
                                    <th class="p-2">Espacios</th>
                                    <th class="p-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rides as $ride)
                                    <tr class="border-b">
                                        <td class="p-2">{{ $ride->nombre }}</td>
                                        <td class="p-2">{{ $ride->origen }}</td>
                                        <td class="p-2">{{ $ride->destino }}</td>
                                        <td class="p-2">{{ $ride->fecha }}</td>
                                        <td class="p-2">{{ $ride->hora }}</td>
                                        <td class="p-2">
                                            {{ $ride->vehiculo->marca }} {{ $ride->vehiculo->modelo }} ({{ $ride->vehiculo->placa }})
                                        </td>
                                        <td class="p-2">₡{{ number_format($ride->costo_por_espacio, 2) }}</td>
                                        <td class="p-2">{{ $ride->espacios }}</td>

                                        <td class="p-2">
                                            {{-- LLAMADA AL MODAL --}}
                                            <a href="#" onclick="openEditModal({{ json_encode($ride) }})"
                                               class="text-blue-600 hover:underline block mb-2">
                                                ✏️ Editar
                                            </a>

                                            {{-- ELIMINAR --}}
                                            <form action="{{ route('rides.destroy', $ride) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Eliminar este ride?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline">
                                                    🗑️ Eliminar
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div id="editRideModal"
         class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white p-6 rounded-lg w-full max-w-xl">
            <h3 class="text-xl font-bold mb-4">Editar Ride</h3>

            <form id="editRideForm" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                {{-- Campos del Ride --}}
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Costo por espacio (₡):</label>
                        <input type="number" step="0.01" id="edit_costo_por_espacio" name="costo_por_espacio"
                               class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Cantidad de espacios:</label>
                        <input type="number" id="edit_espacios" name="espacios" min="1" max="5"
                               class="w-full border p-2 rounded" required>
                    </div>
                </div>

                <div>
                    <label class="font-semibold">Vehículo asociado:</label>
                    <select id="edit_vehiculo_id" name="vehiculo_id" class="w-full border p-2 rounded" required>
                        <option value="">Seleccione un vehículo...</option>
                        @foreach ($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}">
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }})
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Fin Campos del Ride --}}


                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded">
                        Actualizar
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

            // CORRECCIÓN para la hora: elimina los segundos (HH:MM:SS -> HH:MM)
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
