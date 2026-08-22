<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="font-display text-xl">{{ __('Restablecer contraseña') }}</h2>
        <p class="text-sm text-bone-400 mt-2">{{ __('Elegí tu nueva contraseña.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label class="etiqueta" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required autofocus
                value="{{ old('email', $request->email) }}" class="campo">
            @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="password">{{ __('Contraseña nueva') }}</label>
            <input id="password" name="password" type="password" required
                autocomplete="new-password" class="campo">
            @error('password')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="password_confirmation">{{ __('Confirmar contraseña') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                autocomplete="new-password" class="campo">
        </div>

        <button type="submit" class="boton--principal w-full">
            {{ __('Restablecer contraseña') }}
        </button>
    </form>
</x-guest-layout>