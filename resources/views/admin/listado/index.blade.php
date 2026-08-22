<x-app-layout>
    <x-slot name="encabezado">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">{{ __('Operación') }}</p>
                <h1 class="font-display text-display-sm mt-2">{{ __('Listado de reservas') }}</h1>
            </div>
            <form method="GET" action="{{ route('admin.listado.index') }}" class="flex items-end gap-2">
                <div>
                    <label class="etiqueta" for="fecha">{{ __('Fecha') }}</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $fecha }}"
                        class="campo w-auto">
                </div>
                <button type="submit" class="boton--principal">{{ __('Buscar') }}</button>
            </form>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-6xl mx-auto">

            <div class="tarjeta px-5 py-3 mb-6 flex items-center justify-between">
                <div class="text-sm text-stone-600 dark:text-bone-300">
                    <span class="text-stone-500 dark:text-bone-400">{{ __('Fecha') }}:</span>
                    <strong class="font-display text-stone-900 dark:text-bone-100 ml-1">{{ fechaAR($fecha) }}</strong>
                </div>
                <div class="font-mono text-sm text-brass">
                    @if (! empty($reservas))
                        {{ count($reservas) }} {{ count($reservas) === 1 ? __('reserva') : __('reservas') }}
                    @else
                        {{ __('Sin reservas') }}
                    @endif
                </div>
            </div>

            @forelse ($ubicaciones as $ubicacion)
                @php $total = collect($reservasAgrupadas[$ubicacion])->flatten(1)->count(); @endphp
                @if ($total > 0)
                    <section class="mb-8">
                        <div class="flex items-baseline gap-3 mb-4">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brass text-ink-900 font-display font-semibold">
                                {{ $ubicacion }}
                            </span>
                            <h2 class="font-display text-xl">
                                {{ __('reservas.ubicaciones.' . $ubicacion) }}
                            </h2>
                            <span class="text-xs font-mono text-stone-500 dark:text-bone-400">{{ $total }}</span>
                        </div>

                        @foreach ([
                            'manana' => ['label' => __('Mañana'),    'de' => '00:00', 'a' => '13:00'],
                            'tarde'  => ['label' => __('Tarde'),     'de' => '13:00', 'a' => '18:00'],
                            'noche'  => ['label' => __('Noche'),     'de' => '18:00', 'a' => '23:59'],
                        ] as $clave => $info)
                            @if (count($reservasAgrupadas[$ubicacion][$clave]) > 0)
                                <div class="mb-5 ml-4 sm:ml-12">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-[11px] font-mono uppercase tracking-[0.18em] text-stone-500 dark:text-bone-400">{{ $info['label'] }}</span>
                                        <span class="text-[11px] font-mono text-stone-400 dark:text-bone-500">{{ $info['de'] }}–{{ $info['a'] }}</span>
                                    </div>

                                    <div class="tarjeta overflow-hidden">
                                        <table class="w-full">
                                            <thead>
                                                <tr class="border-b border-ink-600 text-left">
                                                    <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400">{{ __('Código') }}</th>
                                                    <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400">{{ __('Cliente') }}</th>
                                                    <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400 hidden sm:table-cell">{{ __('Hora') }}</th>
                                                    <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400">{{ __('Mesas') }}</th>
                                                    <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400 hidden sm:table-cell">{{ __('Personas') }}</th>
                                                    <th class="px-4 py-2 text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400 hidden md:table-cell">{{ __('Estado') }}</th>
                                                    <th class="px-4 py-2 text-right text-[10px] font-mono uppercase tracking-wider text-stone-500 dark:text-bone-400"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-ink-600">
                                                @foreach ($reservasAgrupadas[$ubicacion][$clave] as $r)
                                                    <tr>
                                                        <td class="px-4 py-3 font-mono text-sm text-stone-600 dark:text-bone-300">#{{ $r->id }}</td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <div class="text-stone-900 dark:text-bone-100">{{ $r->cliente_nombre }}</div>
                                                            <div class="text-[11px] text-stone-500 dark:text-bone-400">{{ $r->cliente_email }}</div>
                                                        </td>
                                                        <td class="px-4 py-3 font-mono text-sm text-stone-600 dark:text-bone-300 hidden sm:table-cell">
                                                            {{ substr($r->hora_inicio, 0, 5) }}–{{ substr($r->hora_fin, 0, 5) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach (explode(', ', $r->mesas) as $mesa)
                                                                    <span class="font-mono text-[11px] px-1.5 py-0.5 rounded bg-stone-200 dark:bg-ink-700 text-stone-800 dark:text-bone-200">{{ trim($mesa) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 font-mono text-sm text-stone-600 dark:text-bone-300 hidden sm:table-cell">{{ $r->cantidad_personas }}</td>
                                                        <td class="px-4 py-3 text-sm hidden md:table-cell">
                                                            @if ($r->estado === 'cancelada')
                                                                <span class="text-[10px] uppercase tracking-wider text-red-300">{{ __('Cancelada') }}</span>
                                                            @else
                                                                <span class="text-[10px] uppercase tracking-wider text-brass">{{ __('OK') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 text-right">
                                                            @if ($r->estado === 'confirmada')
                                                                <form action="{{ route('admin.reservas.cancelar', $r->id) }}" method="POST" class="inline"
                                                                    x-data x-on:submit.prevent="
                                                                        Swal.fire({
                                                                            icon: 'warning',
                                                                            title: '{{ __('¿Cancelar #:id?', ["id" => $r->id]) }}',
                                                                            showCancelButton: true,
                                                                            confirmButtonText: '{{ __("Cancelar") }}',
                                                                            cancelButtonText: '{{ __("Volver") }}',
                                                                            confirmButtonColor: '#dc2626',
                                                                            background: dark ? '#1a1f1d' : '#ffffff',
                                                                            color: dark ? '#e8e0d4' : '#1c1917',
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) $el.submit();
                                                                        })
                                                                    ">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="text-[11px] uppercase tracking-wider text-red-300 hover:text-red-200">
                                                                        {{ __('Cancelar') }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </section>
                @endif
            @empty
                <div class="tarjeta p-8 text-center text-stone-500 dark:text-bone-400">
                    {{ __('No hay ubicaciones.') }}
                </div>
            @endforelse

            @if (collect($reservasAgrupadas)->flatten(2)->isEmpty())
                <div class="tarjeta p-12 text-center">
                    <p class="text-stone-600 dark:text-bone-300">{{ __('No hay reservas para esta fecha.') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>