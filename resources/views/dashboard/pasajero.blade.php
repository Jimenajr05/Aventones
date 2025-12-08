@php
    use Illuminate\Support\Facades\Auth;
    // La variable mensaje es crucial para mostrar alertas
    $mensaje = session('success') ?? session('status') ?? session('msg'); 
@endphp

<x-app-layout> 
    
    <section class="w-11/12 max-w-screen-xl mx-auto mt-24 bg-white p-6 sm:p-10 rounded-2xl shadow-[0_8px_25px_rgba(0,0,0,0.08)] text-center">
        
        <h1 class="text-3xl font-bold mb-6 text-[#0B3D2E]">Panel del PASAJERO</h1>

        @if ($mensaje)
            {{-- Mensaje de alerta --}}
            <p class="bg-[#e8f9ef] border border-[#28a745] text-[#155724] p-3 rounded-lg font-bold mb-6">
                {{ htmlspecialchars($mensaje) }}
            </p>
        @endif

        <div class="flex flex-wrap justify-center gap-8">
            
            {{-- Opción 1: Buscar Rides --}}
            <div class="flex-1 min-w-[280px] max-w-sm bg-[#fafafa] rounded-xl p-6 shadow-[0_6px_18px_rgba(0,0,0,0.07)] transition duration-300 hover:transform hover:-translate-y-1 hover:hover:shadow-[0_10px_25px_rgba(0,0,0,0.12)]">
                <h2 class="text-[#0B3D2E] mb-2 text-xl font-semibold">🔍 Buscar Rides</h2>
                <p class="text-[#555] text-base mb-5">Encuentra viajes disponibles cerca de ti y reserva fácilmente.</p>
                
               <a href="{{ route('pasajero.buscar_rides') }}" 
                class="inline-block py-2 px-5 rounded-full no-underline 
                        text-white font-semibold 
                        bg-blue-700 transition duration-300 hover:bg-blue-700"> Buscar Rides
                </a>
            </div>

            {{-- Opción 2: Mis Reservas --}}
            <div class="flex-1 min-w-[280px] max-w-sm bg-[#fafafa] rounded-xl p-6 shadow-[0_6px_18px_rgba(0,0,0,0.07)] transition duration-300 hover:transform hover:-translate-y-1 hover:shadow-[0_10px_25px_rgba(0,0,0,0.12)]">
                <h2 class="text-[#0B3D2E] mb-2 text-xl font-semibold">🎟️ Mis Reservas</h2>
                <p class="text-[#555] text-base mb-5">Consulta y gestiona las reservas que hayas realizado.</p>
                
                <a href="{{ route('reservas.pasajero') }}" 
                   class="inline-block py-2 px-5 rounded-full no-underline 
                          text-white font-semibold 
                          bg-green-600 transition duration-300 hover:bg-green-700">
                    Ver Reservas
                </a>
            </div>

        </div>
        
    </section>
    
    <div class="pb-16"></div> 

</x-app-layout>
