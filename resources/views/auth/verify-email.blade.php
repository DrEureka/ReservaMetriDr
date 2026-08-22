<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="font-display text-xl">{{ __('Verificá tu email') }}</h2>
        <p class="text-sm text-bone-400 mt-2">
            {{ __('Te mandamos un link de verificación. Revisá tu casilla.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-md border border-brass bg-brass/10 px-4 py-3 text-sm text-brass text-center">
            {{ __('Se reenvió el link de verificación.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
        @csrf
        <button type="submit" class="boton--principal w-full">
            {{ __('Reenviar email de verificación') }}
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-xs text-bone-400 hover:text-brass">
            {{ __('Cerrar sesión') }}
        </button>
    </form>
</x-guest-layout>