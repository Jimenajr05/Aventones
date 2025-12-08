<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aventones</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

    <main class="container">

        <section class="intro">
            <p>Con Aventones puedes compartir viajes de manera cómoda, económica y segura.</p>
        </section>

        <section class="busqueda">
            <h2>Buscar Rides Disponibles</h2>

            {{-- La acción del formulario es correcta para la vista pública --}}
            <form method="GET" action="{{ route('public.index') }}" class="form-busqueda">

                <div class="campo">
                    <label>Origen:</label>
                    <input type="text" name="origen" value="{{ request('origen') }}" placeholder="Ej: San Carlos">
                </div>

                <div class="campo">
                    <label>Destino:</label>
                    <input type="text" name="destino" value="{{ request('destino') }}" placeholder="Ej: Alajuela">
                </div>
                
                <div class="campo">
                    <label>Ordenar por:</label>
                    <select name="orden" class="select">
                        <option value="fecha"  {{ request('orden') == 'fecha' ? 'selected' : '' }}>Fecha</option>
                        <option value="hora"   {{ request('orden') == 'hora' ? 'selected' : '' }}>Hora</option>
                        <option value="origen" {{ request('orden') == 'origen' ? 'selected' : '' }}>Origen</option>
                        <option value="destino" {{ request('orden') == 'destino' ? 'selected' : '' }}>Destino</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Dirección:</label>
                    <select name="direccion" class="select">
                        <option value="asc"  {{ request('direccion') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ request('direccion') == 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>
                </div>

                <!-- Botón Buscar -->
                <button class="btn btn-grid">Buscar</button>

                <!-- Botón Limpiar -->
                <button type="button" class="btn btn-secundario btn-grid"
                        onclick="window.location.href='{{ route('public.index') }}'">
                    Limpiar
                </button>
            </form>


            <div id="map-hint">🗺️ Selecciona en el mapa <b>origen</b> y <b>destino</b> dentro de Alajuela.</div>
            <div id="map"></div>

            <h3>Resultados:</h3>

            @if ($rides->isEmpty())
                <p class="no-resultados">🚗 No se encontraron rides disponibles.</p>
            @else
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Vehículo</th>
                                <th>Costo</th>
                                <th>Espacios</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($rides as $ride)
                                <tr>
                                    <td>{{ $ride->nombre }}</td>
                                    <td>{{ $ride->origen }}</td>
                                    <td>{{ $ride->destino }}</td>
                                    <td class="tabla-fecha">{{ $ride->fecha }}</td>
                                    <td class="tabla-hora">{{ \Carbon\Carbon::parse($ride->hora)->format('h:i A') }}</td>
                                    {{-- Acceder a marca y modelo del objeto vehículo --}}
                                    <td class="tabla-vehiculo">{{ $ride->vehiculo->marca ?? 'N/A' }} {{ $ride->vehiculo->modelo ?? '' }}</td>
                                    
                                    {{-- Usar el campo de costo correcto --}}
                                    <td>₡{{ number_format($ride->costo_por_espacio ?? 0, 2) }}</td>
                                    
                                    <td>{{ $ride->espacios }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </section>

        {{-- Muestra Iniciar Sesión/Registrarse solo si NO está logueado (@guest) --}}
        @guest
            <section class="acciones">
                <a href="{{ route('login') }}" class="btn">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="btn btn-secundario">Registrarse</a>
            </section>
        @endguest
        
        {{-- Muestra el mensaje y botón de Panel si está logueado --}}
        @auth
            <section class="acciones">
                <p style="margin-bottom: 10px; font-weight: 600;">
                    ¡Hola, {{ Auth::user()->nombre }}! Ya estás en sesión.
                </p>

                <div style="text-align: center;">
                    @php
                        $role = Auth::user()->role_id;
                    @endphp

                    {{-- SUPER ADMIN --}}
                    @if($role == 1)
                        <a href="{{ route('superadmin.dashboard') }}" class="btn" style="display: inline-block;">
                            Ir a mi Panel SuperAdmin
                        </a>

                    {{-- ADMIN --}}
                    @elseif($role == 2)
                        <a href="{{ route('admin.dashboard') }}" class="btn" style="display: inline-block;">
                            Ir a mi Panel Admin
                        </a>

                    {{-- CHOFER --}}
                    @elseif($role == 3)
                        <a href="{{ route('chofer.dashboard') }}" class="btn" style="display: inline-block;">
                            Ir a mi Panel Chofer
                        </a>

                    {{-- PASAJERO --}}
                    @elseif($role == 4)
                        <a href="{{ route('pasajero.dashboard') }}" class="btn" style="display: inline-block;">
                            Ir a mi Panel Pasajero
                        </a>

                    {{-- CUALQUIER OTRO ROL (por si agregas más) --}}
                    @else
                        <a href="{{ route('dashboard') }}" class="btn" style="display: inline-block;">
                            Ir a mi Panel Principal
                        </a>
                    @endif
                </div>
            </section>
        @endauth


        {{-- Muestra la sección Admin solo si NO está logueado (@guest) --}}
        @guest
            <section class="info-admin">
                <h3>¿Eres administrador?</h3>
                <p>Accede con tus credenciales asignadas.</p>
                <a href="/super-admin/dashboard" class="link-admin">Ir al panel administrativo</a>
            </section>
        @endguest

    </main>

    <script>
        const map = L.map('map').setView([10.01625, -84.21163], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        let mInicio = null, mFin = null;
        let paso = "origen";
        const hint = document.getElementById("map-hint");

        function esAlajuela(txt) {
            return txt.toLowerCase().includes("alajuela");
        }

        async function reverse(lat, lng) {
            const r = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
            const d = await r.json();
            return d.display_name || `${lat}, ${lng}`;
        }

        map.on("click", async e => {
            const { lat, lng } = e.latlng;
            const dir = await reverse(lat, lng);

            if (!esAlajuela(dir)) {
                hint.classList.add("map-error");
                hint.innerHTML = "❌ Solo se permiten ubicaciones dentro de <b>Alajuela</b>";
                return;
            } else {
                hint.classList.remove("map-error");
            }

            if (paso === "origen") {
                if (mInicio) map.removeLayer(mInicio);
                mInicio = L.marker([lat, lng]).addTo(map).bindPopup("📍 Origen").openPopup();
                document.querySelector("[name='origen']").value = dir;
                paso = "destino";
                hint.innerHTML = "📍 Ahora selecciona el <b>destino</b>.";
            } else {
                if (mFin) map.removeLayer(mFin);
                mFin = L.marker([lat, lng]).addTo(map).bindPopup("🏁 Destino").openPopup();
                document.querySelector("[name='destino']").value = dir;
                paso = "origen";
                hint.innerHTML = "✅ Ubicaciones listas. Haz clic en buscar.";
            }
        });
    </script>

</body>
</html>
