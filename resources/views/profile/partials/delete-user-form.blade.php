<section>
    <header class="mb-6">
        <h2 class="font-display text-lg">{{ __('Eliminar cuenta') }}</h2>
        <p class="text-sm text-bone-400 mt-1">{{ __('Esta acción es irreversible. Todos tus datos se borrarán permanentemente.') }}</p>
    </header>

    <button type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="boton--peligro">
        {{ __('Eliminar mi cuenta') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('delete')

            <h2 class="font-display text-lg">{{ __('¿Eliminar tu cuenta?') }}</h2>
            <p class="text-sm text-bone-400">{{ __('Escribí tu contraseña para confirmar.') }}</p>

            <div>
                <input id="password" name="password" type="password"
                    placeholder="{{ __('Contraseña') }}" autocomplete="current-password"
                    class="campo">
                @error('password', 'userDeletion')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" x-on:click="$dispatch('close')" class="boton--fantasma">
                    {{ __('Cancelar') }}
                </button>
                <button type="submit" class="boton--peligro">
                    {{ __('Eliminar') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>