<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div class="text-center mb-2">
            <h2 class="font-display text-xl">{{ __('Entrar') }}</h2>
            <p class="text-[11px] font-mono text-bone-400 mt-1">{{ __('Panel de administración') }}</p>
        </div>

        <div>
            <label class="etiqueta" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required autofocus autocomplete="username"
                value="{{ old('email') }}" placeholder="admin@email.com" class="campo">
            @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="password">{{ __('Contraseña') }}</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="campo">
            @error('password')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-ink-500 bg-ink-700 text-brass focus:ring-brass focus:ring-offset-ink-800">
                <span class="text-sm text-bone-400">{{ __('Recordarme') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-brass hover:text-brass-400">
                    {{ __('¿Olvidaste la contraseña?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="boton--principal w-full">
            {{ __('Entrar') }}
        </button>

        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
        @error('cf-turnstile-response')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror

        <p class="text-center text-xs text-bone-400 pt-2 border-t border-ink-600">
            {{ __('¿No tenés cuenta?') }}
            <a href="{{ route('register') }}" class="text-brass hover:text-brass-400">{{ __('Registrate') }}</a>
        </p>
    </form>
</x-guest-layout>