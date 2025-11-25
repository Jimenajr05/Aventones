<x-app-layout>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Panel del SUPER ADMIN</h1>

        <div class="space-x-4">

            <a href="{{ route('administradores.gestionUsuarios') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">
                Gestión de Usuarios
            </a>

            <a href="{{ route('admin.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">
                Registrar Admin
            </a>

        </div>
    </div>
</x-app-layout>
