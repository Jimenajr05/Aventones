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

            {{-- FORMULARIO PARA CREAR/EDITAR --}}
            <div class="p-6 bg-white rounded-lg shadow">
                
                <h3 id="form-title" class="text-xl font-bold mb-4">Crear nuevo ride</h3>

                <form id="rideForm" action="{{ route('rides.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <input type="hidden" id="edit_id" name="ride_id">

                    <div>
                        <label class="font-semibold">Nombre del ride:</label>
                        <input type="text" id="nombre" name="nombre"
                            class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Lugar de salida:</label>
                        <input type="text" id="origen" name="origen"
                            class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Lugar de llegada:</label>
                        <input type="text" id="destino" name="destino"
                            class="w-full border p-2 rounded" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold">Fecha:</label>
                            <input type="date" id="fecha" name="fecha"
                                   class="w-full border p-2 rounded" required>
                        </div>

                        <div>
                            <label class="font-semibold">Hora:</label>
                            <input type="time" id="hora" name="hora"
                                   class="w-full border p-2 rounded" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold">Costo por espacio (₡):</label>
                            <input type="number" step="0.01" id="costo_por_espacio" name="costo_por_espacio"
                                   class="w-full border p-2 rounded" required>
                        </div>

                        <div>
                            <label class="font-semibold">Cantidad de espacios:</label>
                            <input type="number" id="espacios" name="espacios" min="1" max="5"
                                   class="w-full border p-2 rounded" required>
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold">Vehículo asociado:</label>
                        <select id="vehiculo_id" name="vehiculo_id" class="w-full border p-2 rounded" required>
                            <option value="">Seleccione un vehículo...</option>
                            @foreach ($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->id }}">
                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button id="submitButton" type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                            🚙 Crear ride
                        </button>

                        {{-- Botón cancelar edición --}}
                        <button type="button" id="cancelEditBtn"
                            class="hidden bg-gray-400 hover:bg-gray-500 text-white font-semibold px-4 py-2 rounded"
                            onclick="resetForm()">
                            Cancelar
                        </button>
                    </div>

                </form>
            </div>

            {{-- TABLA --}}
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
                                            {{-- EDITAR --}}
                                            <button onclick="editRide({{ $ride }})"
                                                class="text-blue-600 hover:underline block mb-2">
                                                ✏️ Editar
                                            </button>

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

    {{-- SCRIPT PARA EDITAR --}}
    <script>
        function editRide(ride) {
            document.getElementById("form-title").innerText = "Editar Ride";
            document.getElementById("submitButton").innerText = "Guardar cambios";

            document.getElementById("edit_id").value = ride.id;
            document.getElementById("rideForm").action = "/rides/" + ride.id;

            document.getElementById("nombre").value = ride.nombre;
            document.getElementById("origen").value = ride.origen;
            document.getElementById("destino").value = ride.destino;
            document.getElementById("fecha").value = ride.fecha;
            document.getElementById("hora").value = ride.hora;
            document.getElementById("costo_por_espacio").value = ride.costo_por_espacio;
            document.getElementById("espacios").value = ride.espacios;
            document.getElementById("vehiculo_id").value = ride.vehiculo_id;

            document.getElementById("rideForm").insertAdjacentHTML("beforeend",
                `<input type="hidden" name="_method" value="PATCH">`
            );

            document.getElementById("cancelEditBtn").classList.remove("hidden");
        }

        function resetForm() {
            location.reload();
        }
    </script>

</x-app-layout>
