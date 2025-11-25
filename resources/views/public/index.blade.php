<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aventones</title>

    <!-- Tu CSS original -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="container">

        <section class="intro">
            <p>Con Aventones puedes compartir viajes de manera cómoda, económica y segura.</p>
        </section>

        <!-- BUSCADOR -->
        <section class="busqueda">
            <h2>Buscar Rides Disponibles</h2>

            <form method="GET" action="{{ route('public.index') }}" class="form-busqueda">
                <div class="campo">
                    <label>Origen:</label>
                    <input type="text" name="origen" value="{{ request('origen') }}" placeholder="Ej: San Carlos">
                </div>

                <div class="campo">
                    <label>Destino:</label>
                    <input type="text" name="destino" value="{{ request('destino') }}" placeholder="Ej: Alajuela">
                </div>

                <div class="acciones-form">
                    <button class="btn">Buscar</button>
                    <button type="button" class="btn btn-secundario"
                            onclick="window.location.href='{{ route('public.index') }}'">Limpiar</button>
                </div>
            </form>

            <!-- MAPA -->
            <div id="map-hint">🗺️ Selecciona en el mapa <b>origen</b> y <b>destino</b> dentro de Alajuela.</div>
            <div id="map"></div>

            <!-- RESULTADOS -->
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
                                    <td>{{ $ride->fecha }}</td>
                                    <td>{{ $ride->hora }}</td>
                                    <td>{{ $ride->vehiculo }}</td>
                                    <td>₡{{ number_format($ride->costo, 2) }}</td>
                                    <td>{{ $ride->espacios }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </section>

        <!-- BOTONES LOGIN/REGISTRO -->
        <section class="acciones">
            <a href="{{ route('login') }}" class="btn">Iniciar Sesión</a>
            <a href="{{ route('register') }}" class="btn btn-secundario">Registrarse</a>
        </section>

        <!-- ADMIN -->
        <section class="info-admin">
            <h3>¿Eres administrador?</h3>
            <p>Accede con tus credenciales asignadas.</p>
            <a href="/super-admin/dashboard" class="link-admin">Ir al panel administrativo</a>
        </section>

    </main>

    <!-- SCRIPT MAPA -->
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
