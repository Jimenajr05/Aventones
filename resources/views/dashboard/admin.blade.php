<x-app-layout>

    {{-- BLOQUE DE MENSAJES FLASH (Éxito y Error) --}}
    @if (session('success'))
        <div class="w-11/12 max-w-screen-xl mx-auto mt-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-md" role="alert">
            <p class="font-bold flex items-center">
                <span class="text-xl mr-2">✅</span>
                ¡Comando Ejecutado!
            </p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if (session('error'))
        <div class="w-11/12 max-w-screen-xl mx-auto mt-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-md" role="alert">
            <p class="font-bold flex items-center">
                <span class="text-xl mr-2">❌</span>
                ¡Error!
            </p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <section class="w-11/12 max-w-screen-xl mx-auto mt-24 bg-white p-6 sm:p-10 rounded-2xl 
                    shadow-[0_8px_25px_rgba(0,0,0,0.08)] text-center">

        <h1 class="text-3xl font-bold mb-6 text-[#0B3D2E]">Panel del Administrador</h1>

        <div class="flex flex-wrap justify-center gap-8">

            {{-- Opción 1: Gestión de Usuarios --}}
            <div class="flex-1 min-w-[280px] max-w-sm bg-[#fafafa] rounded-xl p-6 
                        shadow-[0_6px_18px_rgba(0,0,0,0.07)]
                        transition duration-300 hover:-translate-y-1 hover:shadow-[0_10px_25px_rgba(0,0,0,0.12)]">
                
                <h2 class="text-[#0B3D2E] mb-2 text-xl font-semibold">👥 Gestión de Usuarios</h2>
                <p class="text-[#555] text-base mb-5">Administra todos los usuarios registrados en la plataforma.</p>

                <a href="{{ route('administradores.gestionUsuarios') }}"
                   class="inline-block py-2 px-5 rounded-full text-white font-semibold 
                          bg-indigo-600 transition duration-300 hover:bg-indigo-700">
                    Gestionar
                </a>
            </div>

            {{-- Opción 2: Registrar Admin --}}
            <div class="flex-1 min-w-[280px] max-w-sm bg-[#fafafa] rounded-xl p-6 
                        shadow-[0_6px_18px_rgba(0,0,0,0.07)]
                        transition duration-300 hover:-translate-y-1 hover:shadow-[0_10px_25px_rgba(0,0,0,0.12)]">
                
                <h2 class="text-[#0B3D2E] mb-2 text-xl font-semibold">🧑‍💼 Registrar Admin</h2>
                <p class="text-[#555] text-base mb-5">Crea nuevos administradores con permisos especiales.</p>

                <a href="{{ route('admin.create') }}"
                   class="inline-block py-2 px-5 rounded-full text-white font-semibold 
                          bg-green-600 transition duration-300 hover:bg-green-700">
                    Registrar
                </a>
            </div>
            
            {{-- Opción 3: Ejecutar Script de Recordatorio --}}
            <div class="flex-1 min-w-[280px] max-w-sm bg-[#fafafa] rounded-xl p-6 
                        shadow-[0_6px_18px_rgba(0,0,0,0.07)]
                        transition duration-300 hover:-translate-y-1 hover:shadow-[0_10px_25px_rgba(0,0,0,0.12)]">
                
                <h2 class="text-[#0B3D2E] mb-2 text-xl font-semibold">⏰ Notificar Reservas</h2>
                <p class="text-[#555] text-base mb-5">Ejecuta el script para recordar a choferes sobre reservas pendientes.</p>

                {{-- FORMULARIO CON LÓGICA DE CARGA JS --}}
                <form id="reminder-form" method="POST" action="{{ route('admin.execute.reservation_reminder') }}"
                      onsubmit="return confirmExecute(event)">
                    @csrf
                    <button type="submit" id="reminder-button"
                       class="inline-block py-2 px-5 rounded-full text-white font-semibold 
                              bg-orange-500 transition duration-300 hover:bg-orange-600 w-full">
                        Notificar
                    </button>
                    
                    {{-- MENSAJE DE CARGA OCULTO --}}
                    <div id="loading-message" class="hidden mt-2 p-2 text-sm text-center text-orange-700 bg-orange-100 rounded-lg">
                        ⏳ Ejecutando comando... por favor, espere.
                    </div>
                </form>
            </div>

        </div>
    </section>
    
    {{-- Lógica JavaScript para el estado "Cargando" --}}
    <script>
        function confirmExecute(event) {
            // Muestra la ventana de confirmación nativa.
            if (confirm('¿Estás seguro de que deseas ejecutar el script de recordatorio de reservas? Esto puede tardar unos segundos.')) {
                // Si el usuario presiona Aceptar, cambiamos la interfaz
                const button = document.getElementById('reminder-button');
                const loadingMessage = document.getElementById('loading-message');

                // Mantener el color naranja, pero deshabilitar y cambiar texto
                button.disabled = true;
                button.innerText = 'Procesando...';
                
                // Quitamos el hover y cambiamos el cursor para indicar que está deshabilitado
                button.classList.remove('hover:bg-orange-700');
                button.classList.add('cursor-not-allowed'); 
                
                // Mostrar el mensaje de carga
                loadingMessage.classList.remove('hidden');

                return true; // Permitir que la forma se envíe
            }
            
            return false; // Detener el envío de la forma
        }
    </script>

    <div class="pb-16"></div>

</x-app-layout>