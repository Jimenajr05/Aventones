<x-app-layout>

    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Panel del PASAJERO</h1>

        <div class="flex flex-col gap-4">

            <!-- Buscar Rides -->
            <a href="{{ route('public.index') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded w-fit">
               🔍 Buscar Rides
            </a>

            <!-- Mis Reservas -->
            <a href="{{ route('reservas.pasajero') }}"
               class="bg-green-600 text-white px-4 py-2 rounded w-fit">
               📘 Mis Reservas
            </a>

        </div>
    </div>

</x-app-layout>
