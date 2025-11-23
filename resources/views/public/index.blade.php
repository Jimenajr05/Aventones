<x-guest-layout>

    <div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded-xl shadow">

        <p class="text-center text-gray-700 mb-4">
            Con Aventones puedes compartir viajes de manera cómoda, económica y segura.
        </p>

        <h1 class="text-3xl font-bold text-center mb-8">
            Buscar Rides Disponibles
        </h1>

        <!-- Formulario de búsqueda -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

            <div>
                <label class="font-semibold">Origen:</label>
                <input type="text" name="origen" value="{{ request('origen') }}"
                       placeholder="Ej: San Carlos"
                       class="mt-1 w-full border rounded p-2">
            </div>

            <div>
                <label class="font-semibold">Destino:</label>
                <input type="text" name="destino" value="{{ request('destino') }}"
                       placeholder="Ej: Alajuela"
                       class="mt-1 w-full border rounded p-2">
            </div>

            <div class="flex gap-2 items-end">
                <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                    Buscar
                </button>
                <a href="{{ route('public.index') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded w-full text-center">
                    Limpiar
                </a>
            </div>
        </form>

        <!-- Mensaje de Alerta -->
        <div class="w-full bg-red-100 text-red-700 border border-red-300 p-3 mb-4 rounded text-center font-medium">
            ❌ Solo se permiten ubicaciones dentro de <strong>Alajuela</strong>
        </div>

        <!-- Mapa -->
        <div id="map" style="height: 350px;" class="rounded mb-8"></div>

        <!-- Resultados -->
        <h2 class="font-bold text-lg mb-2">Resultados:</h2>

        @if ($rides->isEmpty())
            <div class="text-center text-gray-600 py-6">
                🚗 No se encontraron rides disponibles con esos criterios.
            </div>

            <div class="flex justify-center gap-4 mt-4">
                <a href="/login" class="bg-blue-600 text-white px-4 py-2 rounded">Iniciar Sesión</a>
                <a href="/register" class="bg-green-600 text-white px-4 py-2 rounded">Registrarse</a>
            </div>

            <div class="text-center mt-6 text-sm">
                <strong>¿Eres administrador?</strong>
                <p class="text-gray-600">Accede con tus credenciales asignadas.</p>
                <a href="/super-admin/dashboard" class="text-blue-600 underline">
                    Ir al panel administrativo
                </a>
            </div>

        @else
            <ul class="space-y-3">
                @foreach ($rides as $ride)
                    <li class="p-4 border rounded bg-gray-50">
                        <strong>{{ $ride->nombre }}</strong><br>
                        Origen: {{ $ride->origen }}<br>
                        Destino: {{ $ride->destino }}<br>
                        Fecha: {{ $ride->fecha }} Hora: {{ $ride->hora }}<br>
                        Costo: ₡{{ $ride->costo }} / espacio
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        let map = L.map('map').setView([10.01625, -84.21163], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
    </script>

</x-guest-layout>
