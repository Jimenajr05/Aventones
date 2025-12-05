<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Vehículos
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto">

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
                               value="{{ old('capacidad') }}"
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


            <div class="p-6 bg-white rounded-lg shadow">

                <h3 class="text-xl font-bold mb-4">Mis Vehículos Registrados</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-gray-500">No tienes vehículos registrados.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($vehiculos as $vehiculo)
                            
                            {{-- INICIO TARJETA DE VEHÍCULO (CORREGIDO: Usando justify-between para alinear los botones a la derecha) --}}
                            <div class="border p-4 rounded-lg shadow-sm flex justify-between items-start space-x-4">
                                
                                <div class="flex space-x-4 flex-grow"> {{-- Contenedor para Imagen y Detalles --}}
                                    <div class="flex-shrink-0">
                                        @if ($vehiculo->fotografia)
                                            <img src="{{ asset('storage/'.$vehiculo->fotografia) }}"
                                                 class="w-24 h-24 object-cover rounded-md">
                                        @else
                                            <span class="text-gray-500 text-sm w-24 h-24 flex items-center justify-center border rounded-md">Sin imagen</span>
                                        @endif
                                    </div>

                                    <div class="flex-grow">
                                        <p class="text-lg font-bold">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                                        
                                        <div class="text-sm text-gray-700 space-y-0.5 mt-1">
                                            <p><span class="font-semibold">Placa:</span> {{ $vehiculo->placa }}</p>
                                            <p><span class="font-semibold">Color:</span> {{ $vehiculo->color }}</p>
                                            <p><span class="font-semibold">Año:</span> {{ $vehiculo->anio }}</p>
                                            <p><span class="font-semibold">Capacidad:</span> {{ $vehiculo->capacidad }} personas</p>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- SECCIÓN DE BOTONES DE ACCIÓN (Alineado a la derecha sin ml-auto) --}}
                                <div class="flex-shrink-0 flex flex-col space-y-4"> {{-- Se quitó ml-auto --}}
                                    
                                    <a href="#" onclick="openEditModal({{ $vehiculo }})"
                                       class="text-sm font-semibold px-4 py-2 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-center whitespace-nowrap">
                                        ✏️ Editar
                                    </a>

                                    <form action="{{ route('vehiculos.destroy', $vehiculo) }}"
                                          method="POST" onsubmit="return confirm('¿Eliminar este vehículo?')"
                                          class="m-0">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="text-sm font-semibold px-4 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white text-center whitespace-nowrap">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                            {{-- FIN TARJETA DE VEHÍCULO --}}

                        @endforeach
                    </div>
                @endif

            </div>

        </div>
    </div>

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

    <script>
        function openEditModal(vehiculo) {
            document.getElementById('edit_marca').value = vehiculo.marca;
            document.getElementById('edit_modelo').value = vehiculo.modelo;
            document.getElementById('edit_placa').value = vehiculo.placa;
            document.getElementById('edit_color').value = vehiculo.color;
            document.getElementById('edit_anio').value = vehiculo.anio;
            document.getElementById('edit_capacidad').value = vehiculo.capacidad;

            // Asegurar que el color seleccionado se muestre correctamente en el modal
            document.getElementById('edit_color').value = vehiculo.color;

            document.getElementById('editForm').action =
                '{{ url('vehiculos') }}/' + vehiculo.id; 

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>

</x-app-layout>