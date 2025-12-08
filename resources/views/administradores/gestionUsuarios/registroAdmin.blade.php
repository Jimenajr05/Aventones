<x-app-layout>
    
    <div class="max-w-3xl mx-auto py-10">

        {{-- Alertas --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-800 p-4 rounded-lg mb-6">
                <ul class="list-disc pl-6 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tarjetas --}}
        <div class="bg-white p-8 rounded-2xl shadow-[0_8px_25px_rgba(0,0,0,0.08)]">

            {{-- Título --}}
            <div class="mb-8 text-center">
                <h2 class="font-bold text-3xl">Registrar Administrador</h2>
                <div class="w-16 h-1 mx-auto mt-2 rounded-full opacity-70"></div>
            </div>

            {{-- Formulario --}}
            <form method="POST"
                  action="{{ route('admin.store') }}"
                  enctype="multipart/form-data"
                  autocomplete="off">

                @csrf

                {{-- Nombre --}}
                <div class="mb-5">
                    <x-input-label for="nombre" :value="__('Nombre:')" />
                    <x-text-input id="nombre" name="nombre" type="text"
                                  class="block w-full mt-1"
                                  autocomplete="off"
                                  value="{{ old('nombre') }}" />
                </div>

                {{-- Apellido --}}
                <div class="mb-5">
                    <x-input-label for="apellido" :value="__('Apellidos:')" />
                    <x-text-input id="apellido" name="apellido" type="text"
                                  class="block w-full mt-1"
                                  autocomplete="off"
                                  value="{{ old('apellido') }}" />
                </div>

                {{-- Correo --}}
                <div class="mb-5">
                    <x-input-label for="email" :value="__('Correo electrónico:')" />
                    <x-text-input id="email" name="email" type="email"
                                  class="block w-full mt-1"
                                  value="{{ old('email') }}" />
                </div>

                {{-- Cédula --}}
                <div class="mb-5">
                    <x-input-label for="cedula" :value="__('Número de cédula:')" />
                    <x-text-input id="cedula" name="cedula" type="text"
                                  class="block w-full mt-1"
                                  autocomplete="off"
                                  value="{{ old('cedula') }}" />
                </div>

                {{-- Fecha de Nacimiento --}}
                <div class="mb-5">
                    <x-input-label for="fecha_nacimiento" :value="__('Fecha de nacimiento:')" />
                    <x-text-input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                                  class="block w-full mt-1"
                                  autocomplete="off"
                                  value="{{ old('fecha_nacimiento') }}" />
                </div>

                {{-- Teléfono --}}
                <div class="mb-5">
                    <x-input-label for="telefono" :value="__('Número de teléfono:')" />
                    <x-text-input id="telefono" name="telefono" type="text"
                                  class="block w-full mt-1"
                                  autocomplete="off"
                                  value="{{ old('telefono') }}" />
                </div>

                {{-- Foto --}}
                <div class="mb-5">
                    <x-input-label for="foto" :value="__('Fotografía de perfil:')" />
                    <input id="foto" name="foto" type="file"
                           class="mt-2 block w-full border border-gray-300 rounded-lg p-2 
                                  focus:ring-indigo-500 focus:border-indigo-500"
                           accept="image/*"
                           autocomplete="off" />
                </div>

                {{-- Contraseña --}}
                <div class="mb-5">
                    <x-input-label for="password" :value="__('Contraseña:')" />
                    <x-text-input id="password" name="password" type="password"
                                  class="block w-full mt-1"
                                  autocomplete="new-password" />
                </div>

                {{-- Confirmar contraseña --}}
                <div class="mb-5">
                    <x-input-label for="password_confirmation" :value="__('Confirmar contraseña:')" />
                    <x-text-input id="password_confirmation" name="password_confirmation"
                                  type="password"
                                  class="block w-full mt-1"
                                  autocomplete="new-password" />
                </div>

                {{-- Botón centrado --}}
                <div class="flex justify-center mt-8">
                    <x-primary-button class="px-6 py-3 text-lg">
                        Registrar Administrador
                    </x-primary-button>
                </div>

            </form>

        </div>

    </div>

</x-app-layout>
