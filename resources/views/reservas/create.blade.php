<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nueva reserva') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('exito'))
                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 p-4 text-sm text-green-800 dark:text-green-200">
                    {{ session('exito') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('reservas.store') }}">
                    @csrf

                    <div class="space-y-5">

                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <input id="fecha" name="fecha" type="date" required
                                min="{{ now()->format('Y-m-d') }}"
                                value="{{ old('fecha', request('fecha', now()->format('Y-m-d'))) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="rango-info">
                                {{ __('Elegí una fecha para ver los horarios disponibles.') }}
                            </p>
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="hora_inicio" :value="__('Hora')" />
                            <select id="hora_inicio" name="hora_inicio" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Elegí primero una fecha') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cantidad_personas" :value="__('Cantidad de personas')" />
                            <input id="cantidad_personas" name="cantidad_personas" type="number" min="1" max="50" required
                                value="{{ old('cantidad_personas', 2) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <x-input-error :messages="$errors->get('cantidad_personas')" class="mt-2" />
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 rounded-md p-3 text-sm text-gray-700 dark:text-gray-300">
                            <p class="font-medium mb-1">{{ __('Datos del local') }}</p>
                            <ul class="text-xs space-y-0.5">
                                <li>{{ __('Duración') }}: {{ __(':minutos minutos', ['minutos' => 120]) }}</li>
                                <li>{{ __('Anticipación mínima') }}: {{ __(':minutos minutos', ['minutos' => 15]) }}</li>
                                <li>{{ __('Máximo de mesas por reserva') }}: 3</li>
                            </ul>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-primary-button type="submit">
                                {{ __('Confirmar reserva') }}
                            </x-primary-button>
                            <a href="{{ route('reservas.mis-reservas') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                {{ __('Ver mis reservas') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Lunes a viernes') }}: 10:00 a 24:00 hs ·
                {{ __('Sábado') }}: 22:00 a 02:00 hs ·
                {{ __('Domingo') }}: 12:00 a 16:00 hs
            </div>
        </div>
    </div>

    <script>
        (function () {
            const inputFecha = document.getElementById('fecha');
            const selectHora = document.getElementById('hora_inicio');
            const rangoInfo = document.getElementById('rango-info');
            const oldHora = @json(old('hora_inicio'));

            const traducciones = {
                elegirPrimero: @json(__('Elegí primero una fecha')),
                cargando: @json(__('Cargando...')),
                sinSlots: @json(__('No hay horarios disponibles para esta fecha.')),
                rangoLunesAViernes: @json(__('Lunes a viernes: 10:00 a 24:00 hs')),
                rangoSabado: @json(__('Sábado: 22:00 a 02:00 hs')),
                rangoDomingo: @json(__('Domingo: 12:00 a 16:00 hs')),
                sinRango: @json(__('No atendemos ese día.')),
            };

            function setSlots(slots) {
                selectHora.innerHTML = '';
                if (!slots.length) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = traducciones.sinSlots;
                    selectHora.appendChild(opt);
                    return;
                }
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '— ' + traducciones.cargando + ' —';
                selectHora.appendChild(placeholder);
                slots.forEach(function (s) {
                    const opt = document.createElement('option');
                    opt.value = s;
                    opt.textContent = s;
                    if (s === oldHora) opt.selected = true;
                    selectHora.appendChild(opt);
                });
            }

            function cargar() {
                const fecha = inputFecha.value;
                if (!fecha) {
                    selectHora.innerHTML = '<option value="">' + traducciones.elegirPrimero + '</option>';
                    rangoInfo.textContent = '';
                    return;
                }
                fetch('/api/horarios?fecha=' + encodeURIComponent(fecha), {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.rango) {
                            const labels = {
                                1: traducciones.rangoLunesAViernes,
                                6: traducciones.rangoSabado,
                                0: traducciones.rangoDomingo,
                            };
                            const dow = new Date(fecha + 'T00:00:00').getDay();
                            rangoInfo.textContent = labels[dow] || traducciones.sinRango;
                        }
                        setSlots(data.slots || []);
                    });
            }

            inputFecha.addEventListener('change', cargar);
            cargar();
        })();
    </script>
</x-app-layout>
