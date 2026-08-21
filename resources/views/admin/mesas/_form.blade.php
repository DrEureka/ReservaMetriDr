@php
    $esEdicion = isset($mesa);
@endphp

<div class="space-y-4">
    <div>
        <x-input-label for="ubicacion" :value="__('Ubicación')" />
        <select id="ubicacion" name="ubicacion"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            required>
            <option value="">— Seleccionar —</option>
            @foreach ($ubicaciones as $ubi)
                <option value="{{ $ubi }}" @selected(old('ubicacion', $esEdicion ? $mesa->ubicacion : '') === $ubi)>
                    {{ __('Sección :letra', ['letra' => $ubi]) }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('ubicacion')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="numero" :value="__('Número de mesa')" />
        <x-text-input id="numero" name="numero" type="number" min="1" max="999"
            :value="old('numero', $esEdicion ? $mesa->numero : '')"
            class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('numero')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="capacidad" :value="__('Capacidad (personas)')" />
        <x-text-input id="capacidad" name="capacidad" type="number" min="1" max="50"
            :value="old('capacidad', $esEdicion ? $mesa->capacidad : '')"
            class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('capacidad')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3 pt-2">
        <x-primary-button>
            {{ $esEdicion ? __('Actualizar mesa') : __('Crear mesa') }}
        </x-primary-button>
        <a href="{{ route('admin.mesas.index') }}"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
            {{ __('Cancelar') }}
        </a>
    </div>
</div>
