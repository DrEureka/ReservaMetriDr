<x-app-layout>
    <x-slot name="encabezado">
        <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">{{ __('Admin') }}</p>
        <h1 class="font-display text-display-sm mt-2">{{ __('Dashboard') }}</h1>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="tarjeta p-8 text-center">
                <p class="text-stone-600 dark:text-bone-300">{{ __('Estás logueado.') }}</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('admin.listado.index') }}" class="boton--principal">
                        {{ __('Listado de reservas') }}
                    </a>
                    <a href="{{ route('admin.mesas.index') }}" class="boton--fantasma">
                        {{ __('Gestionar mesas') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>