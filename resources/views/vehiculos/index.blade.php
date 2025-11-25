<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Vehículos
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto">

        <!-- Mensajes -->
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

        <!-- Contenedor principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- FORMULARIO -->
            <div class="p-6 bg-white rounded-lg shadow">

                <h3 class="text-xl font-bold mb-4">Registrar nuevo vehículo</h3>

                <form action="{{ route('vehiculos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="font-semibold">Marca:</label>
                        <input type="text" name="marca" value="{{ old('marca') }}" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Modelo:</label>
                        <input type="text" name="modelo" value="{{ old('modelo') }}" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Placa:</label>
                        <input type="text" name="placa" value="{{ old('placa') }}" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Color:</label>
                        <select name="color" class="w-full border p-2 rounded" required>
                            <option value="">Seleccione...</option>
                            @foreach ($colores as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-semibold">Año:</label>
                        <input type="number" name="anio" min="2010" max="2030"
                               value="{{ old('anio') }}"
                               class="w-full border p-2 rounded" required>
                        <small class="text-gray-500">Solo vehículos del 2010 en adelante.</small>
                    </div>

                    <div>
                        <label class="font-semibold">Capacidad:</label>
                        <input type="number" name="capacidad" min="1" max="5"
                               value="{{ old('capacidad', 4) }}"
                               class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label class="font-semibold">Fotografía:</label>
                        <input type="file" name="fotografia" class="w-full border p-2 rounded">
                    </div>

                     <div class="mt-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                            🚗 Registrar Vehículo
                        </button>
                    </div>
                </form>
            </div>


            <!-- TABLA DE VEHÍCULOS -->
            <div class="p-6 bg-white rounded-lg shadow">

                <h3 class="text-xl font-bold mb-4">Mis Vehículos Registrados</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-gray-500">No tienes vehículos registrados.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-200 text-left">
                                    <th class="p-2">Foto</th>
                                    <th class="p-2">Marca</th>
                                    <th class="p-2">Modelo</th>
                                    <th class="p-2">Placa</th>
                                    <th class="p-2">Color</th>
                                    <th class="p-2">Año</th>
                                    <th class="p-2">Capacidad</th>
                                    <th class="p-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($vehiculos as $vehiculo)
                                <tr class="border-b">
                                    <td class="p-2">
                                        @if ($vehiculo->fotografia)
                                            <img src="{{ asset('storage/'.$vehiculo->fotografia) }}"
                                                 class="w-20 h-20 object-cover rounded">
                                        @else
                                            <span class="text-gray-500">Sin imagen</span>
                                        @endif
                                    </td>

                                    <td class="p-2">{{ $vehiculo->marca }}</td>
                                    <td class="p-2">{{ $vehiculo->modelo }}</td>
                                    <td class="p-2">{{ $vehiculo->placa }}</td>
                                    <td class="p-2">{{ $vehiculo->color }}</td>
                                    <td class="p-2">{{ $vehiculo->anio }}</td>
                                    <td class="p-2">{{ $vehiculo->capacidad }}</td>

                                    <td class="p-2">

                                        <!-- EDITAR -->
                                        <a href="#" onclick="openEditModal({{ $vehiculo }})"
                                           class="text-blue-600 font-semibold hover:underline block">
                                            ✏️ Editar
                                        </a>

                                        <!-- ELIMINAR -->
                                        <form action="{{ route('vehiculos.destroy', $vehiculo) }}"
                                              method="POST" onsubmit="return confirm('¿Eliminar este vehículo?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="text-red-600 font-semibold hover:underline mt-1">
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

    <!-- MODAL DE EDICIÓN (Tailwind + JS simple) -->
    <div id="editModal"
         class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">

        <div class="bg-white p-6 rounded-lg w-full max-w-xl">
            <h3 class="text-xl font-bold mb-4">Editar Vehículo</h3>

            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label>Marca:</label>
                        <input type="text" id="edit_marca" name="marca" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label>Modelo:</label>
                        <input type="text" id="edit_modelo" name="modelo" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label>Placa:</label>
                        <input type="text" id="edit_placa" name="placa" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label>Color:</label>
                        <select id="edit_color" name="color" class="w-full border p-2 rounded" required>
                            @foreach ($colores as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Año:</label>
                        <input type="number" id="edit_anio" name="anio" min="2010" max="2030"
                               class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label>Capacidad:</label>
                        <input type="number" id="edit_capacidad" name="capacidad" min="1" max="5"
                               class="w-full border p-2 rounded" required>
                    </div>

                </div>

                <div class="mt-4">
                    <label>Fotografía nueva:</label>
                    <input type="file" name="fotografia" class="border p-2 rounded w-full">
                </div>

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

    <!-- SCRIPT DEL MODAL DE EDICIÓN -->
    <script>
        function openEditModal(vehiculo) {
            document.getElementById('edit_marca').value = vehiculo.marca;
            document.getElementById('edit_modelo').value = vehiculo.modelo;
            document.getElementById('edit_placa').value = vehiculo.placa;
            document.getElementById('edit_color').value = vehiculo.color;
            document.getElementById('edit_anio').value = vehiculo.anio;
            document.getElementById('edit_capacidad').value = vehiculo.capacidad;

            document.getElementById('editForm').action =
                '/vehiculos/' + vehiculo.id;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>

</x-app-layout>