<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="font-display text-xl">{{ __('¿Olvidaste la contraseña?') }}</h2>
        <p class="text-sm text-bone-400 mt-2">
            {{ __('Te mandamos un link para restablecerla.') }}
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-brass bg-brass/10 px-4 py-3 text-sm text-brass">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label class="etiqueta" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required autofocus
                value="{{ old('email') }}" class="campo">
            @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="boton--principal w-full">
            {{ __('Enviar link de restablecimiento') }}
        </button>

        <p class="text-center text-xs text-bone-400 pt-2 border-t border-ink-600">
            <a href="{{ route('login') }}" class="text-brass hover:text-brass-400">
                ← {{ __('Volver al login') }}
            </a>
        </p>
    </form>
</x-guest-layout>