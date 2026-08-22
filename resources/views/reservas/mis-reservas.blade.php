<x-app-layout>
    <x-slot name="encabezado">
        <div class="text-center">
            <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">
                {{ __('Tu historial') }}
            </p>
            <h1 class="font-display text-display-sm sm:text-display-md mt-2">
                {{ __('Mis reservas') }}
            </h1>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">

            @guest
                <div class="tarjeta p-5 sm:p-8 mb-6">
                    <p class="text-stone-500 dark:text-bone-300 text-sm mb-4">
                        {{ __('Ingresá el email con el que hiciste la reserva.') }}
                    </p>
                    <form method="POST" action="{{ route('reservas.mis-reservas.buscar') }}" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="email" name="email" required
                            value="{{ old('email', $email_buscado ?? '') }}"
                            placeholder="tu@email.com" autocomplete="email"
                            class="campo flex-1">
                        <button type="submit" class="boton--principal whitespace-nowrap">
                            {{ __('Buscar') }}
                        </button>
                    </form>
                </div>

                @if (($no_encontrado ?? false) === true)
                    <div class="tarjeta p-5 text-center">
                        <p class="text-stone-500 dark:text-bone-300">
                            {{ __('No hay reservas con ese email.') }}
                        </p>
                        <a href="{{ route('reservas.create') }}" class="mt-3 inline-block text-brass hover:underline">
                            {{ __('Hacé tu primera reserva →') }}
                        </a>
                    </div>
                @endif
            @endguest

            @if (isset($reservas) && $reservas !== null && $reservas->count())
                <div class="tarjeta divide-y divide-stone-200 dark:divide-ink-600">
                    @foreach ($reservas as $reserva)
                        <article class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-center gap-4 sm:flex-col sm:items-start sm:gap-2 sm:w-32">
                                <span class="font-mono text-stone-500 dark:text-bone-400 text-xs">
                                    #{{ $reserva->id }}
                                </span>
                                <div class="flex sm:flex-col gap-2 sm:gap-1">
                                    <span class="font-display text-lg leading-none">{{ fechaAR($reserva->fecha) }}</span>
                                    <span class="font-mono text-sm text-stone-500 dark:text-bone-400">
                                        {{ substr($reserva->hora_inicio, 0, 5) }}–{{ substr($reserva->hora_fin, 0, 5) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex-1 grid grid-cols-2 gap-2 sm:gap-4 text-sm">
                                <div>
                                    <div class="etiqueta !mb-0">{{ __('Sección') }}</div>
                                    <div>{{ __('reservas.ubicaciones.' . $reserva->ubicacion) }}</div>
                                </div>
                                <div>
                                    <div class="etiqueta !mb-0">{{ __('Mesas') }}</div>
                                    <div class="font-mono">{{ $reserva->nombresMesas() }}</div>
                                </div>
                                <div>
                                    <div class="etiqueta !mb-0">{{ __('Personas') }}</div>
                                    <div class="mesa-num">{{ $reserva->cantidad_personas }}</div>
                                </div>
                                <div>
                                    <div class="etiqueta !mb-0">{{ __('Estado') }}</div>
                                    @if ($reserva->estaCancelada())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] uppercase tracking-wider bg-red-950 border border-red-900 text-red-200">
                                            {{ __('Cancelada') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] uppercase tracking-wider bg-brass/15 border border-brass/30 text-brass">
                                            {{ __('Confirmada') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="sm:w-32 sm:text-right">
                                @auth
                                    @if ($reserva->estaConfirmada())
                                        <form action="{{ route('admin.reservas.cancelar', $reserva) }}" method="POST"
                                            x-data x-on:submit.prevent="
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: '{{ __('¿Cancelar la reserva #:id?', ["id" => $reserva->id]) }}',
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
                                        <button type="submit" class="boton--peligro w-full sm:w-auto">
                                            {{ __('Cancelar') }}
                                        </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-stone-500 dark:text-bone-400">—</span>
                                    @endif
                                @else
                                    <span class="text-xs text-stone-500 dark:text-bone-400">{{ __('Vía mail') }}</span>
                                @endauth
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">{{ $reservas->links() }}</div>
            @elseif (isset($reservas) && $reservas !== null && $reservas->count() === 0)
                <div class="tarjeta p-8 text-center">
                    <p class="text-stone-500 dark:text-bone-300">
                        @auth
                            {{ __('Todavía no tenés reservas.') }}
                        @else
                            {{ __('No hay reservas con ese email.') }}
                        @endauth
                    </p>
                    <a href="{{ route('reservas.create') }}" class="mt-3 inline-block text-brass hover:underline">
                        {{ __('Hacé tu primera reserva →') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>