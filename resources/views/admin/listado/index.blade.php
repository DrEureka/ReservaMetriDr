<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Listado de reservas') }}
            </h2>
            <form method="GET" action="{{ route('admin.listado.index') }}" class="flex items-center gap-2">
                <x-input-label for="fecha" :value="__('Fecha')" class="!mb-0" />
                <input id="fecha" name="fecha" type="date" value="{{ $fecha }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                <x-primary-button type="submit" class="!py-1.5 !px-3 !text-xs">
                    {{ __('Buscar') }}
                </x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('exito'))
                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 p-4 text-sm text-green-800 dark:text-green-200">
                    {{ session('exito') }}
                </div>
            @endif

            <div class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Mostrando reservas para') }} <strong>{{ fechaAR($fecha) }}</strong>
                @if (! empty($reservas))
                    — {{ __(':total reserva(s) en total', ['total' => count($reservas)]) }}
                @else
                    — {{ __('sin reservas') }}
                @endif
            </div>

            @forelse ($ubicaciones as $ubicacion)
                @php
                    $totalUbicacion = collect($reservasAgrupadas[$ubicacion])->flatten(1)->count();
                @endphp
                @if ($totalUbicacion > 0)
                    <section class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 font-bold">
                                {{ $ubicacion }}
                            </span>
                            {{ __('reservas.ubicaciones.' . $ubicacion) }}
                            <span class="text-xs text-gray-500">({{ $totalUbicacion }})</span>
                        </h3>

                        @foreach (['manana' => __('Mañana'), 'tarde' => __('Tarde'), 'noche' => __('Noche')] as $claveTurno => $nombreTurno)
                            @if (count($reservasAgrupadas[$ubicacion][$claveTurno]) > 0)
                                <div class="mb-4 ml-4">
                                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">
                                        {{ $nombreTurno }}
                                    </h4>
                                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Código') }}
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Cliente') }}
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Hora') }}
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Mesas') }}
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Personas') }}
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Estado') }}
                                                    </th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        {{ __('Acciones') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach ($reservasAgrupadas[$ubicacion][$claveTurno] as $reserva)
                                                    <tr>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            #{{ $reserva->id }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $reserva->cliente_nombre }}
                                                            <span class="block text-xs text-gray-500">{{ $reserva->cliente_email }}</span>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                            {{ substr($reserva->hora_inicio, 0, 5) }}–{{ substr($reserva->hora_fin, 0, 5) }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            <span class="inline-flex flex-wrap gap-1">
                                                                @foreach (explode(', ', $reserva->mesas) as $mesa)
                                                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-xs">{{ trim($mesa) }}</span>
                                                                @endforeach
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $reserva->cantidad_personas }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                                            @if ($reserva->estado === 'cancelada')
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                    {{ __('Cancelada') }}
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                                    {{ __('Confirmada') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-right text-sm">
                                                            @if ($reserva->estado === 'confirmada')
                                                                <form action="{{ route('admin.reservas.cancelar', $reserva->id) }}" method="POST" class="inline"
                                                                    onsubmit="return confirm('{{ __('¿Cancelar la reserva #:id?', ['id' => $reserva->id]) }}')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">
                                                                        {{ __('Cancelar') }}
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-gray-400">—</span>
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
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sin ubicaciones definidas.') }}</p>
            @endforelse

            @if (collect($reservasAgrupadas)->flatten(2)->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('No hay reservas para esta fecha.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
