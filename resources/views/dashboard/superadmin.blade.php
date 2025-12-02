<x-app-layout>

    <section class="w-11/12 max-w-screen-xl mx-auto mt-24 bg-white p-6 sm:p-10 rounded-2xl 
                    shadow-[0_8px_25px_rgba(0,0,0,0.08)] text-center">

        <h1 class="text-3xl font-bold mb-6 text-[#0B3D2E]">Panel del Super administrador</h1>

        <div class="flex flex-wrap justify-center gap-8">

            {{-- Opción 1: Gestión de Usuarios --}}
            <div class="flex-1 min-w-[280px] max-w-sm bg-[#fafafa] rounded-xl p-6 
                        shadow-[0_6px_18px_rgba(0,0,0,0.07)]
                        transition duration-300 hover:-translate-y-1 hover:shadow-[0_10px_25px_rgba(0,0,0,0.12)]">
                
                <h2 class="text-[#0B3D2E] mb-2 text-xl font-semibold">👥 Gestión de Usuarios</h2>
                <p class="text-[#555] text-base mb-5">Supervisa y gestiona todos los usuarios y administradores.</p>

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
                <p class="text-[#555] text-base mb-5">Crea nuevos administradores con todos los permisos.</p>

                <a href="{{ route('admin.create') }}"
                   class="inline-block py-2 px-5 rounded-full text-white font-semibold 
                          bg-green-600 transition duration-300 hover:bg-green-700">
                    Registrar 
                </a>
            </div>

        </div>
    </section>

    <div class="pb-16"></div>

</x-app-layout>