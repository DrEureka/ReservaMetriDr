<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nueva reserva') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    {{ __('El formulario de reserva se implementa en Parte 8.') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
