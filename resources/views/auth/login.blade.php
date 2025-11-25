<x-guest-layout>

    <!-- Botón para volver al index -->
    <div class="mb-4 text-center">
        <a href="{{ route('public.index') }}" 
           class="text-blue-600 hover:text-blue-800 underline text-sm">
            ← Volver al inicio
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" 
                          type="email" name="email"
                          :value="old('email')" 
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" 
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Recordar') }}</span>
            </label>
        </div>

        <!-- Registrar cuenta -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-700">
                ¿No tienes cuenta aún?
                <a href="{{ route('register') }}" class="text-blue-600 underline hover:text-blue-800">
                    Regístrate aquí
                </a>
            </p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-4">

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900"
                   href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Iniciar Sesión') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
