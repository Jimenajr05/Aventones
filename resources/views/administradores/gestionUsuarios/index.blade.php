<x-app-layout>

    <div class="w-11/12 max-w-6xl mx-auto mt-10">

        {{-- Alertas --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Contenedor principal --}}
        <div class="bg-white rounded-2xl shadow-[0_8px_25px_rgba(0,0,0,0.08)] p-6">

            {{-- Título --}}
            <h1 class="text-3xl font-extrabold text-[#0B3D2E] mb-8 text-center">
                Gestión de Usuarios
            </h1>

            <table class="w-full border-collapse text-left">

                {{-- Encabezados --}}
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 font-semibold text-gray-700">ID</th>
                        <th class="p-3 font-semibold text-gray-700">Nombre</th>
                        <th class="p-3 font-semibold text-gray-700">Correo</th>
                        <th class="p-3 font-semibold text-gray-700">Tipo</th>
                        <th class="p-3 font-semibold text-gray-700">Estado</th>
                        <th class="p-3 font-semibold text-gray-700 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-50 transition">

                            <td class="p-3">{{ $user->id }}</td>

                            <td class="p-3 font-medium text-gray-800">
                                {{ $user->nombre }} {{ $user->apellido }}
                            </td>

                            <td class="p-3 text-gray-600">{{ $user->email }}</td>

                            {{-- Badge Rol --}}
                            <td class="p-3">
                                @php
                                    $roles = [
                                        1 => ['Super Administrador'],
                                        2 => ['Administrador'],
                                        3 => ['Chofer'],
                                        4 => ['Pasajero'],
                                    ];
                                @endphp

                               
                            <span class="text-gray-900 font-semibold">
                                {{ $roles[$user->role_id][0] }}
                            </span>
                                                    
                            </td>

                            {{-- Badge Estado --}}
                            <td class="p-3">
                                @php
                                    $estados = [
                                        1 => ['Pendiente', 'bg-gray-200 text-gray-700'],
                                        2 => ['Activo', 'bg-green-200 text-green-800'],
                                        3 => ['Inactivo', 'bg-red-200 text-red-800'],
                                    ];
                                @endphp

                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $estados[$user->status_id][1] }}">
                                    {{ $estados[$user->status_id][0] }}
                                </span>
                            </td>

                            {{-- Acciones --}}
                            <td class="p-3 text-center">
                                <div class="flex justify-center gap-2">

                                    @if(!$user->is_super_admin)

                                        {{-- Activar --}}
                                        @if($user->status_id != 2)
                                            <form method="POST" action="{{ route('administradores.gestionUsuarios.activate', $user->id) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-700 transition text-sm">
                                                    Activar
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Desactivar --}}
                                        @if($user->status_id != 3)
                                            <form method="POST" action="{{ route('administradores.gestionUsuarios.deactivate', $user->id) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700 transition text-sm">
                                                    Desactivar
                                                </button>
                                            </form>
                                        @endif

                                    @else
                                        <span class="text-gray-400 text-sm italic">No permitido</span>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="pb-16"></div>

    </div>

</x-app-layout>
