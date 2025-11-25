<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

    @php
        // Ruta principal del panel según rol
        $dashboardRoute = [
            1 => 'superadmin.dashboard',
            2 => 'admin.dashboard',
            3 => 'chofer.dashboard',
            4 => 'pasajero.dashboard',
        ][Auth::user()->role_id] ?? 'login';

        // Botones dinámicos según rol
        $roleButtons = [
            1 => [
                ['label' => 'Gestión de Usuarios', 'route' => 'administradores.gestionUsuarios'],
            ],
            2 => [
                ['label' => 'Gestión de Usuarios', 'route' => 'administradores.gestionUsuarios'],
            ],
            3 => [
                ['label' => 'Mis Vehículos', 'route' => 'vehiculos.index'],
                ['label' => 'Mis Rides', 'route' => 'rides.index'],
                ['label' => 'Reservas Recibidas', 'route' => 'reservas.chofer'],
            ],
            4 => [
                ['label' => 'Buscar Rides', 'route' => 'public.index'],
                ['label' => 'Mis Reservas', 'route' => 'reservas.pasajero'],
            ],
        ];
    @endphp


    <!-- CONTENEDOR PRINCIPAL -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- IZQUIERDA: Panel + Botones -->
            <div class="hidden sm:flex sm:items-center space-x-8">

                <!-- PANEL PRINCIPAL -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route($dashboardRoute)"
                        :active="request()->routeIs($dashboardRoute)">
                        Panel Principal
                    </x-nav-link>
                </div>

                <!-- BOTONES DINÁMICOS (estilo Jetstream) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @foreach ($roleButtons[Auth::user()->role_id] ?? [] as $btn)
                        <x-nav-link :href="route($btn['route'])"
                            :active="request()->routeIs($btn['route'])">
                            {{ $btn['label'] }}
                        </x-nav-link>
                    @endforeach
                </div>

            </div>

            <!-- DERECHA: Bienvenida + Foto + Ajustes -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6">

                <!-- Bienvenida + Foto -->
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 text-md">
                        Bienvenido, <strong>{{ Auth::user()->nombre }}</strong>
                    </span>

                    <img src="{{ Auth::user()->foto 
                        ? asset('storage/foto_usuarios/' . Auth::user()->foto) 
                        : asset('images/default-user.png') }}"
                        class="w-12 h-12 rounded-full object-cover border shadow">
                </div>

                <!-- Ajustes -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="p-2 rounded hover:bg-gray-100 transition">
                            <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5z" />
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .67.39 1.27 1 1.51H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Editar Perfil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- HAMBUERGUESA (MÓVIL) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 
                    hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- MENU RESPONSIVE -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <!-- PANEL PRINCIPAL -->
        <x-responsive-nav-link :href="route($dashboardRoute)"
            :active="request()->routeIs($dashboardRoute)">
            Panel Principal
        </x-responsive-nav-link>

        <!-- BOTONES DINÁMICOS RESPONSIVE -->
        @foreach ($roleButtons[Auth::user()->role_id] ?? [] as $btn)
            <x-responsive-nav-link :href="route($btn['route'])"
                :active="request()->routeIs($btn['route'])">
                {{ $btn['label'] }}
            </x-responsive-nav-link>
        @endforeach

        <!-- USUARIO -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    {{ Auth::user()->nombre }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Editar Perfil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

</nav>