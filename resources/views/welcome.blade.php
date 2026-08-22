<x-app-layout>
    <x-slot name="encabezado">
        <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">{{ __('Reservas') }}</p>
        <h1 class="font-display text-display-sm mt-2">{{ __('Elegí tu mesa') }}</h1>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-12 text-center">
        <p class="text-bone-300">{{ __('Redirigiendo…') }}</p>
    </div>
</x-app-layout>