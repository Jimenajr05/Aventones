<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Información de perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Actualiza la información personal de tu cuenta.') }}
        </p>
    </header>

    {{-- Formulario para reenviar verificación --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- FORMULARIO PRINCIPAL --}}
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Nombre --}}
        <div>
            <x-input-label for="nombre" :value="__('Nombre')" />
            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                :value="old('nombre', $user->nombre)" required autocomplete="given-name" />
            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
        </div>

        {{-- Apellido --}}
        <div>
            <x-input-label for="apellido" :value="__('Apellido')" />
            <x-text-input id="apellido" name="apellido" type="text" class="mt-1 block w-full"
                :value="old('apellido', $user->apellido)" required autocomplete="family-name" />
            <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
        </div>

        {{-- Número de cédula (NO editable) --}}
        <div>
            <x-input-label for="cedula" :value="__('Número de cédula')" />
            <x-text-input id="cedula" type="text"
                class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="$user->cedula" disabled />
            <x-input-error class="mt-2" :messages="$errors->get('cedula')" />
        </div>

        {{-- Fecha de nacimiento --}}
        <div>
            <x-input-label for="fecha_nacimiento" :value="__('Fecha de nacimiento')" />
            <x-text-input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                class="mt-1 block w-full"
                :value="old('fecha_nacimiento', $user->fecha_nacimiento)" required />
            <x-input-error class="mt-2" :messages="$errors->get('fecha_nacimiento')" />
        </div>

        {{-- Teléfono --}}
        <div>
            <x-input-label for="telefono" :value="__('Número de teléfono')" />
            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full"
                :value="old('telefono', $user->telefono)" required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
        </div>

        {{-- Correo electrónico (SOLO LECTURA) --}}
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="$user->email" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        {{-- Fotografía actual (CORREGIDA CON ESTILO EN LÍNEA FORZADO) --}}
        @if(Auth::user()->foto)
            <img src="{{ asset('storage/' . Auth::user()->foto) }}" 
                class="w-24 h-24 rounded-full border shadow object-cover mb-4">
        @endif


        {{-- Subir nueva fotografía --}}
        <div>
            <x-input-label for="foto" :value="__('Fotografía personal')" />
            <input id="foto" name="foto" type="file" class="mt-1 block w-full text-sm">
            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
        </div>

        {{-- Botón final --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar cambios') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">
                    {{ __('Guardado.') }}
                </p>
            @endif
        </div>

    </form>
</section>