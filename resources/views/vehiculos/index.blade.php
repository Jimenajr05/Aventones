<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Vehículos
        </h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto">

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        <div class="space-y-10">

            {{-- Formulario --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                
                <h3 class="text-2xl font-bold text-center mb-6">Registrar nuevo vehículo</h3>

                <div class="max-w-4xl mx-auto">
                    <form action="{{ route('vehiculos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" novalidate>
                        @csrf

                        {{-- Fila 1 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="font-semibold">Marca:</label>
                                <input type="text" name="marca" value="{{ old('marca') }}"
                                       class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                <label class="font-semibold">Modelo:</label>
                                <input type="text" name="modelo" value="{{ old('modelo') }}"
                                       class="w-full border p-2 rounded-lg" required>
                            </div>

                            <div>
                                <label class="font-semibold">Placa:</label>
                                <input type="text" name="placa" value="{{ old('placa') }}"
                                       class="w-full border p-2 rounded-lg" required>
                            </div>
                        </div>

                        {{-- Fila 2 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="font-semibold">Color:</label>
                                <select name="color" class="w-full border p-2 rounded-lg" required>
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
                                       class="w-full border p-2 rounded-lg" required>
                                <small class="text-gray-500">Desde 2010.</small>
                            </div>

                            <div>
                                <label class="font-semibold">Capacidad:</label>
                                <input type="number" min="1" max="5" name="capacidad"
                                       value="{{ old('capacidad') }}"
                                       class="w-full border p-2 rounded-lg" required>
                            </div>
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="font-semibold">Fotografía:</label>
                            <input type="file" name="fotografia" class="w-full border p-2 rounded-lg">
                        </div>

                        {{-- Botón --}}
                        <div class="mt-4 flex justify-center">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 shadow transition duration-300">
                                Registrar Vehículo
                            </button>
                        </div>
                    </form>
                </div>

            </div>


            {{-- Listado --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">

                <h3 class="text-2xl font-bold text-center mb-6">Mis Vehículos Registrados</h3>

                @if ($vehiculos->isEmpty())
                    <p class="text-gray-500 text-center">No tienes vehículos registrados.</p>
                @else
                    <div class="grid grid-cols-1 gap-4">

                        @foreach ($vehiculos as $vehiculo)

                            <div class="p-4 bg-white rounded-lg shadow border border-gray-100 flex items-start space-x-4">

                                {{-- Foto --}}
                                <div class="flex-shrink-0">
                                    @if ($vehiculo->fotografia)
                                        <img src="{{ asset('storage/'.$vehiculo->fotografia) }}"
                                             class="w-24 h-24 object-cover rounded-md shadow">
                                    @else
                                        <div class="w-24 h-24 bg-gray-100 rounded-md flex items-center justify-center text-gray-500 text-xs text-center">
                                            Sin imagen
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-grow space-y-1">
                                    <h4 class="text-lg font-bold text-gray-800">
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                    </h4>

                                    <p class="text-sm text-gray-700"><span class="font-semibold">Placa:</span> {{ $vehiculo->placa }}</p>
                                    <p class="text-sm text-gray-700"><span class="font-semibold">Color:</span> {{ $vehiculo->color }}</p>
                                    <p class="text-sm text-gray-700"><span class="font-semibold">Año:</span> {{ $vehiculo->anio }}</p>
                                    <p class="text-sm text-gray-700"><span class="font-semibold">Capacidad:</span> {{ $vehiculo->capacidad }} personas</p>
                                </div>

                                {{-- Botones --}}
                                <div class="flex-shrink-0 flex flex-col space-y-4">

                                    <button onclick="openEditModal({{ $vehiculo }})"
                                        class="inline-block py-2 px-4 text-sm rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">
                                        ✏️ Editar
                                    </button>

                                    <form action="{{ route('vehiculos.destroy', $vehiculo) }}"
                                          method="POST" onsubmit="return confirm('¿Eliminar este vehículo?')" novalidate>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-block py-2 px-4 text-sm rounded-full bg-red-600 text-white font-semibold hover:bg-red-700 shadow transition">
                                            🗑️ Eliminar
                                        </button>
                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>
                @endif

            </div>

        </div>

    </div>


    {{-- Modal --}}
    <div id="editModal"
         class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-xl">

            <h3 class="text-xl font-bold text-center mb-4">Editar Vehículo</h3>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold">Marca:</label>
                        <input type="text" id="edit_marca" name="marca" class="w-full border p-2 rounded-lg" required>
                    </div>

                    <div>
                        <label class="font-semibold">Modelo:</label>
                        <input type="text" id="edit_modelo" name="modelo" class="w-full border p-2 rounded-lg" required>
                    </div>

                    <div>
                        <label class="font-semibold">Placa:</label>
                        <input type="text" id="edit_placa" name="placa" class="w-full border p-2 rounded-lg" required>
                    </div>

                    <div>
                        <label class="font-semibold">Color:</label>
                        <select id="edit_color" name="color" class="w-full border p-2 rounded-lg" required>
                            @foreach ($colores as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-semibold">Año:</label>
                        <input type="number" id="edit_anio" name="anio" min="2010" max="2030"
                               class="w-full border p-2 rounded-lg" required>
                    </div>

                    <div>
                        <label class="font-semibold">Capacidad:</label>
                        <input type="number" id="edit_capacidad" name="capacidad" min="1" max="5"
                               class="w-full border p-2 rounded-lg" required>
                    </div>
                </div>

                <div>
                    <label class="font-semibold">Fotografía nueva:</label>
                    <input type="file" name="fotografia" class="border p-2 rounded-lg w-full">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Actualizar
                    </button>
                </div>

            </form>

        </div>

    </div>

    {{-- Script --}}
    <script>
        function openEditModal(vehiculo) {
            document.getElementById('edit_marca').value = vehiculo.marca;
            document.getElementById('edit_modelo').value = vehiculo.modelo;
            document.getElementById('edit_placa').value = vehiculo.placa;
            document.getElementById('edit_color').value = vehiculo.color;
            document.getElementById('edit_anio').value = vehiculo.anio;
            document.getElementById('edit_capacidad').value = vehiculo.capacidad;

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
