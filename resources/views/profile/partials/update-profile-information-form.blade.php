<section>
    <header class="mb-6">
        <h2 class="font-display text-lg">{{ __('Datos de la cuenta') }}</h2>
        <p class="text-sm text-bone-400 mt-1">{{ __('Actualizá tu nombre y email.') }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label class="etiqueta" for="name">{{ __('Nombre') }}</label>
            <input id="name" name="name" type="text" required autofocus autocomplete="name"
                value="{{ old('name', $user->name) }}" class="campo">
            @error('name')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="etiqueta" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required autocomplete="username"
                value="{{ old('email', $user->email) }}" class="campo">
            @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-sm text-bone-400">
                    {{ __('Tu email no está verificado.') }}
                    <button form="send-verification" class="text-brass hover:text-brass-400 underline">
                        {{ __('Reenviar verificación') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="text-brass mt-1">{{ __('Se reenvió el link.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="boton--principal">{{ __('Guardar') }}</button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-brass">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>