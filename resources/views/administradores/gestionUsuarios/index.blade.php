<x-app-layout>

    <div class="p-6">

        <h1 class="text-2xl font-bold mb-6">Gestión de Usuarios</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">ID</th>
                    <th class="p-2">Nombre</th>
                    <th class="p-2">Correo</th>
                    <th class="p-2">Rol</th>
                    <th class="p-2">Estado</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b">
                        <td class="p-2">{{ $user->id }}</td>
                        <td class="p-2">{{ $user->nombre }} {{ $user->apellido }}</td>
                        <td class="p-2">{{ $user->email }}</td>
                        <td class="p-2">
                            @switch($user->role_id)
                                @case(1) Super Admin @break
                                @case(2) Admin @break
                                @case(3) Chofer @break
                                @case(4) Pasajero @break
                            @endswitch
                        </td>
                        <td class="p-2">
                            @switch($user->status_id)
                                @case(1) Pendiente @break
                                @case(2) Activo @break
                                @case(3) Inactivo @break
                            @endswitch
                        </td>

                        <td class="p-2 flex gap-2">

                            @if(!$user->is_super_admin)

                                @if($user->status_id != 2)
                                    <form method="POST" action="{{ route('administradores.gestionUsuarios.activate', $user->id) }}">
                                        @csrf
                                        <button class="bg-green-500 text-white px-3 py-1 rounded">Activar</button>
                                    </form>
                                @endif

                                @if($user->status_id != 3)
                                    <form method="POST" action="{{ route('administradores.gestionUsuarios.deactivate', $user->id) }}">
                                        @csrf
                                        <button class="bg-red-500 text-white px-3 py-1 rounded">Desactivar</button>
                                    </form>
                                @endif

                            @else
                                <span class="text-gray-400">No permitido</span>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</x-app-layout>