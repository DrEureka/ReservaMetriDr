<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="text-center mb-2">
            <h2 class="font-display text-xl">{{ __('Crear cuenta') }}</h2>
            <p class="text-[11px] font-mono text-bone-400 mt-1">{{ __('Solo administradores') }}</p>
        </div>

        <div>
            <label class="etiqueta" for="name">{{ __('Nombre') }}</label>
            <input id="name" name="name" type="text" required autofocus autocomplete="name"
                value="{{ old('name') }}" class="campo">
            @error('name')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required autocomplete="username"
                value="{{ old('email') }}" class="campo">
            @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="password">{{ __('Contraseña') }}</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                class="campo">
            @error('password')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="password_confirmation">{{ __('Confirmar contraseña') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                autocomplete="new-password" class="campo">
        </div>

        <div class="flex justify-center">
            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
        </div>

        <button type="submit" class="boton--principal w-full">
            {{ __('Crear cuenta') }}
        </button>

        <p class="text-center text-xs text-bone-400 pt-2 border-t border-ink-600">
            {{ __('¿Ya tenés cuenta?') }}
            <a href="{{ route('login') }}" class="text-brass hover:text-brass-400">{{ __('Entrá') }}</a>
        </p>
    </form>
</x-guest-layout>