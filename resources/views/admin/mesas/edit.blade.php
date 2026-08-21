<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar mesa :nombre', ['nombre' => $mesa->nombreCompleto()]) }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.mesas.update', $mesa) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.mesas._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
