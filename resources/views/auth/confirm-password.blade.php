<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="font-display text-xl">{{ __('Confirmar contraseña') }}</h2>
        <p class="text-sm text-bone-400 mt-2">
            {{ __('Para continuar, confirmá tu contraseña.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label class="etiqueta" for="password">{{ __('Contraseña') }}</label>
            <input id="password" name="password" type="password" required
                autocomplete="current-password" class="campo">
            @error('password')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="boton--principal w-full">
            {{ __('Confirmar') }}
        </button>
    </form>
</x-guest-layout>