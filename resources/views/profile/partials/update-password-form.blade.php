<section>
    <header class="mb-6">
        <h2 class="font-display text-lg">{{ __('Contraseña') }}</h2>
        <p class="text-sm text-bone-400 mt-1">{{ __('Cambiá tu contraseña para mantener la cuenta segura.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label class="etiqueta" for="update_password_current_password">{{ __('Contraseña actual') }}</label>
            <input id="update_password_current_password" name="current_password" type="password"
                autocomplete="current-password" class="campo">
            @error('current_password', 'updatePassword')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="update_password_password">{{ __('Contraseña nueva') }}</label>
            <input id="update_password_password" name="password" type="password"
                autocomplete="new-password" class="campo">
            @error('password', 'updatePassword')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="update_password_password_confirmation">{{ __('Confirmar contraseña') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password" class="campo">
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="boton--principal">{{ __('Guardar') }}</button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-brass">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>