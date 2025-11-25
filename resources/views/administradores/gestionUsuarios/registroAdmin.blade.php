<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar Administrador
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6">

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
                <ul class="list-disc px-6">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" 
              action="{{ route('admin.store') }}" 
              enctype="multipart/form-data"
              autocomplete="off">

            @csrf

            <!-- Nombre -->
            <div class="mt-4">
                <x-input-label for="nombre" :value="__('Nombre')" />
                <x-text-input id="nombre" type="text" name="nombre"
                              class="block mt-1 w-full"
                              autocomplete="new-name" />
            </div>

            <!-- Apellido -->
            <div class="mt-4">
                <x-input-label for="apellido" :value="__('Apellido')" />
                <x-text-input id="apellido" type="text" name="apellido"
                              class="block mt-1 w-full"
                              autocomplete="new-family-name" />
            </div>

            <!-- Correo -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-text-input id="email" type="email" name="email"
                              class="block mt-1 w-full"
                              autocomplete="nope" />
            </div>

            <!-- Cédula -->
            <div class="mt-4">
                <x-input-label for="cedula" :value="__('Número de cédula')" />
                <x-text-input id="cedula" type="text" name="cedula"
                              class="block mt-1 w-full"
                              autocomplete="nope" />
            </div>

            <!-- Fecha de nacimiento -->
            <div class="mt-4">
                <x-input-label for="fecha_nacimiento" :value="__('Fecha de nacimiento')" />
                <x-text-input id="fecha_nacimiento" type="date" name="fecha_nacimiento"
                              class="block mt-1 w-full"
                              autocomplete="nope" />
            </div>

            <!-- Teléfono -->
            <div class="mt-4">
                <x-input-label for="telefono" :value="__('Número de teléfono')" />
                <x-text-input id="telefono" type="text" name="telefono"
                              class="block mt-1 w-full"
                              autocomplete="nope" />
            </div>

            <!-- Foto -->
            <div class="mt-4">
                <x-input-label for="foto" :value="__('Fotografía de perfil')" />
                <input id="foto" type="file" name="foto"
                       class="block mt-1 w-full"
                       accept="image/*"
                       autocomplete="off" />
            </div>

            <!-- Contraseña -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input id="password" type="password" name="password"
                              class="block mt-1 w-full"
                              autocomplete="new-password" />
            </div>

            <!-- Confirmar contraseña -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                <x-text-input id="password_confirmation" type="password"
                              name="password_confirmation"
                              class="block mt-1 w-full"
                              autocomplete="new-password" />
            </div>

            <!-- Botón -->
            <div class="flex items-center justify-end mt-6">
                <x-primary-button>
                    Registrar Administrador
                </x-primary-button>
            </div>

        </form>
    </div>

</x-app-layout>
