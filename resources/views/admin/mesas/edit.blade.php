<x-app-layout>
    <x-slot name="encabezado">
        <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">{{ __('Configuración') }}</p>
        <h1 class="font-display text-display-sm mt-2">
            {{ __('Editar mesa :nombre', ['nombre' => $mesa->nombreCompleto()]) }}
        </h1>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-xl mx-auto">
            <div class="tarjeta p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.mesas.update', $mesa) }}" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <label class="etiqueta" for="ubicacion">{{ __('Ubicación') }}</label>
                        <select id="ubicacion" name="ubicacion" required class="campo">
                            @foreach ($ubicaciones as $u)
                                <option value="{{ $u }}" @selected(old('ubicacion', $mesa->ubicacion) === $u)>
                                    {{ __('Sección :letra', ['letra' => $u]) }}
                                </option>
                            @endforeach
                        </select>
                        @error('ubicacion')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="etiqueta" for="numero">{{ __('Número') }}</label>
                            <input id="numero" name="numero" type="number" min="1" max="999" required
                                value="{{ old('numero', $mesa->numero) }}" class="campo mesa-num">
                            @error('numero')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="etiqueta" for="capacidad">{{ __('Capacidad') }}</label>
                            <input id="capacidad" name="capacidad" type="number" min="1" max="50" required
                                value="{{ old('capacidad', $mesa->capacidad) }}" class="campo mesa-num">
                            @error('capacidad')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-ink-600">
                        <a href="{{ route('admin.mesas.index') }}" class="boton--fantasma flex-1 text-center">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" class="boton--principal flex-1">
                            {{ __('Guardar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>