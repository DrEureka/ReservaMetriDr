<x-app-layout>
    <x-slot name="encabezado">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">{{ __('Configuración') }}</p>
                <h1 class="font-display text-display-sm mt-2">{{ __('Mesas') }}</h1>
            </div>
            <a href="{{ route('admin.mesas.create') }}" class="boton--principal self-start sm:self-auto">
                {{ __('Nueva mesa') }}
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-6xl mx-auto">

            @php $porSeccion = $mesas->groupBy('ubicacion'); @endphp

            <div class="space-y-8">
                @foreach (['A', 'B', 'C', 'D'] as $sec)
                    @if (isset($porSeccion[$sec]))
                        <section>
                            <div class="flex items-baseline gap-3 mb-3">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brass text-ink-900 font-display font-semibold">
                                    {{ $sec }}
                                </span>
                                <h2 class="font-display text-xl">{{ __('Sección :letra', ['letra' => $sec]) }}</h2>
                                <span class="text-xs font-mono text-stone-500 dark:text-bone-400">{{ $porSeccion[$sec]->count() }}</span>
                            </div>

                            <div class="tarjeta overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-ink-600 text-left">
                                            <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400">{{ __('Número') }}</th>
                                            <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400">{{ __('Capacidad') }}</th>
                                            <th class="px-4 py-2 text-right text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400">{{ __('Acciones') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-ink-600">
                                        @foreach ($porSeccion[$sec] as $mesa)
                                            <tr>
                                                <td class="px-4 py-3 font-mono text-sm text-stone-900 dark:text-bone-100">{{ $mesa->numero }}</td>
                                                <td class="px-4 py-3 font-mono text-sm text-stone-600 dark:text-bone-300">{{ $mesa->capacidad }} {{ __('personas') }}</td>
                                                <td class="px-4 py-3 text-right text-sm space-x-3">
                                                    <a href="{{ route('admin.mesas.edit', $mesa) }}" class="text-brass hover:text-brass-400">{{ __('Editar') }}</a>
                                                    <form action="{{ route('admin.mesas.destroy', $mesa) }}" method="POST" class="inline"
                                                        x-data x-on:submit.prevent="
                                                            Swal.fire({
                                                                icon: 'warning',
                                                                title: '{{ __('¿Eliminar la mesa :nombre?', ["nombre" => $mesa->nombreCompleto()]) }}',
                                                                showCancelButton: true,
                                                                confirmButtonText: '{{ __("Eliminar") }}',
                                                                cancelButtonText: '{{ __("Volver") }}',
                                                                confirmButtonColor: '#dc2626',
                                                                background: dark ? '#1a1f1d' : '#ffffff',
                                                                color: dark ? '#e8e0d4' : '#1c1917',
                                                            }).then((result) => {
                                                                if (result.isConfirmed) $el.submit();
                                                            })
                                                        ">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-300 hover:text-red-200">{{ __('Eliminar') }}</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif
                @endforeach
            </div>

            @if ($mesas->isEmpty())
                <div class="tarjeta p-8 text-center">
                    <p class="text-stone-600 dark:text-bone-300">{{ __('Todavía no hay mesas cargadas.') }}</p>
                    <a href="{{ route('admin.mesas.create') }}" class="mt-3 inline-block text-brass hover:underline">
                        {{ __('Cargar primera mesa →') }}
                    </a>
                </div>
            @endif

            <div class="mt-6">{{ $mesas->links() }}</div>
        </div>
    </div>
</x-app-layout>