<x-app-layout>
    <x-slot name="encabezado">
        <div class="text-center">
            <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">
                {{ __('Reservas') }}
            </p>
            <h1 class="font-display text-display-sm sm:text-display-md mt-2">
                {{ __('Elegí tu mesa') }}
            </h1>
            <p class="text-stone-500 dark:text-bone-300 mt-2 max-w-md mx-auto">
                {{ __('Fecha, hora y cuántas personas. Te confirmamos por mail en segundos.') }}
            </p>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="max-w-3xl mx-auto">

            <div class="tarjeta p-5 sm:p-8">
                <form method="POST" action="{{ route('reservas.store') }}" class="space-y-7" id="form-reserva">
                    @csrf

                    @guest
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-2 border-b border-ink-600">
                            <div>
                                <label class="etiqueta" for="nombre">{{ __('Tu nombre') }}</label>
                                <input id="nombre" name="nombre" type="text" required minlength="2" maxlength="100"
                                    value="{{ old('nombre') }}" autocomplete="name"
                                    class="campo">
                                @error('nombre')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="etiqueta" for="email">{{ __('Tu email') }}</label>
                                <input id="email" name="email" type="email" required maxlength="255"
                                    value="{{ old('email') }}" autocomplete="email"
                                    class="campo">
                                @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                            <p class="sm:col-span-2 -mt-3 text-xs text-stone-500 dark:text-bone-400">
                                {{ __('Te enviamos la confirmación acá.') }}
                            </p>
                        </div>
                    @endguest

                    {{-- Fecha --}}
                    <div>
                        <label class="etiqueta" for="fecha">{{ __('Fecha') }}</label>
                        <input id="fecha" name="fecha" type="date" required
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('fecha', request('fecha', now()->format('Y-m-d'))) }}"
                            class="campo">
                        @error('fecha')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>

                    {{-- Personas: stepper --}}
                    <div>
                        <label class="etiqueta">{{ __('Personas') }}</label>
                        <div class="flex items-center gap-3">
                            <button type="button" id="personas-minus"
                                class="stepper-btn">−</button>
                            <div class="flex-1 text-center">
                                <span id="personas-display" class="font-mono text-2xl text-stone-900 dark:text-bone-100">{{ old('cantidad_personas', 2) }}</span>
                            </div>
                            <button type="button" id="personas-plus"
                                class="stepper-btn">+</button>
                        </div>
                        <input type="hidden" name="cantidad_personas" id="cantidad_personas"
                            value="{{ old('cantidad_personas', 2) }}">
                        @error('cantidad_personas')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>

                    {{-- Slots --}}
                    <div>
                        <div class="flex items-baseline justify-between mb-3">
                            <span class="etiqueta !mb-0">{{ __('Hora') }}</span>
                            <span class="text-[11px] text-stone-500 dark:text-bone-400" id="rango-info"></span>
                        </div>

                        <div id="slots-wrap" class="grid grid-cols-2 sm:grid-cols-4 gap-2 min-h-[48px]">
                            <div class="col-span-full flex items-center justify-center py-6 hidden" id="slots-loading">
                                <svg class="animate-spin h-5 w-5 text-brass" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span class="ml-2 text-sm text-stone-500 dark:text-bone-400">{{ __('Cargando…') }}</span>
                            </div>
                            <p class="col-span-full text-sm text-stone-500 dark:text-bone-400 py-6 text-center" id="slots-empty">
                                {{ __('Elegí primero una fecha.') }}
                            </p>
                        </div>

                        <input type="hidden" name="hora_inicio" id="hora_inicio_hidden"
                            value="{{ old('hora_inicio') }}">
                        @error('hora_inicio')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror

                        <p class="mt-3 text-[11px] text-stone-500 dark:text-bone-400 leading-relaxed">
                            {{ __('Cada turno dura 2 horas. Anticipación mínima 15 min.') }}
                            {{ __('Si necesitás más de una mesa, las asignamos consecutivas en la misma sección.') }}
                        </p>
                    </div>

                    <div class="flex justify-center pt-1">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3 pt-2 border-t border-ink-600">
                        <a href="{{ route('reservas.mis-reservas') }}" class="boton--fantasma flex-1 text-center" id="btn-mis-reservas">
                            {{ __('Ver mis reservas') }}
                        </a>
                        <button type="submit" class="boton--principal flex-1" id="btn-confirmar">
                            <span id="btn-text">{{ __('Confirmar reserva') }}</span>
                            <span id="btn-spinner" class="hidden items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ __('Reservando…') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <p class="mt-6 text-center text-[11px] font-mono uppercase tracking-[0.18em] text-stone-500 dark:text-bone-400">
                {{ __('Lun a vie · 10:00 → 24:00') }}<br class="sm:hidden">
                <span class="hidden sm:inline"> &nbsp;·&nbsp; </span>
                {{ __('Sáb · 22:00 → 02:00') }}<br class="sm:hidden">
                <span class="hidden sm:inline"> &nbsp;·&nbsp; </span>
                {{ __('Dom · 12:00 → 16:00') }}
            </p>
        </div>
    </div>

    <script>
    (function () {
        var $fecha       = document.getElementById('fecha');
        var $personasHid = document.getElementById('cantidad_personas');
        var $display     = document.getElementById('personas-display');
        var $btnMinus    = document.getElementById('personas-minus');
        var $btnPlus     = document.getElementById('personas-plus');
        var $wrap        = document.getElementById('slots-wrap');
        var $empty       = document.getElementById('slots-empty');
        var $loading     = document.getElementById('slots-loading');
        var $rangoInfo   = document.getElementById('rango-info');
        var $hidden      = document.getElementById('hora_inicio_hidden');
        var oldHora      = @json(old('hora_inicio'));

        var personas = parseInt($personasHid.value, 10) || 2;

        var T = {
            sinSlots:      @json(__('No hay horarios disponibles.')),
            sinDisp:       @json(__('Sin disponibilidad')),
            cerrado:       @json(__('Cerrado ese día.')),
            lunesAViernes: @json(__('Lun a vie · 10:00 → 24:00')),
            sabado:        @json(__('Sáb · 22:00 → 02:00')),
            domingo:       @json(__('Dom · 12:00 → 16:00')),
        };
        var dowLabels = { 1: T.lunesAViernes, 6: T.sabado, 0: T.domingo };

        function actualizarPersonas(val) {
            personas = Math.max(1, Math.min(30, val));
            $display.textContent = personas;
            $personasHid.value = personas;
        }

        $btnMinus.addEventListener('click', function () {
            actualizarPersonas(personas - 1);
            cargar();
        });
        $btnPlus.addEventListener('click', function () {
            actualizarPersonas(personas + 1);
            cargar();
        });

        function marcar(btn) {
            $wrap.querySelectorAll('.slot').forEach(function (el) {
                el.setAttribute('aria-selected', el === btn ? 'true' : 'false');
            });
            $hidden.value = btn ? btn.dataset.slot : '';
        }

        function mostrarLoading() {
            $empty.style.display = 'none';
            $wrap.querySelectorAll('.slot').forEach(function (el) { el.remove(); });
            $loading.classList.remove('hidden');
        }

        function ocultarLoading() {
            $loading.classList.add('hidden');
        }

        function renderSlots(slots) {
            ocultarLoading();
            $wrap.querySelectorAll('.slot').forEach(function (el) { el.remove(); });

            if (!slots.length) {
                $empty.textContent = T.sinSlots;
                $empty.style.display = '';
                marcar(null);
                return;
            }
            $empty.style.display = 'none';

            slots.forEach(function (s) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.slot = s.hora;
                btn.setAttribute('role', 'radio');
                btn.setAttribute('aria-checked', 'false');

                if (s.disponible) {
                    btn.className = 'slot slot-disponible';
                    btn.innerHTML = '<span class="font-mono mesa-num tracking-tight">' + s.hora + '</span>' +
                        '<span class="slot__capacidad">' + s.total_mesas_libres + ' mesas</span>';
                    btn.addEventListener('click', function () { marcar(btn); });
                } else {
                    btn.className = 'slot slot-agotado';
                    btn.disabled = true;
                    btn.innerHTML = '<span class="font-mono mesa-num tracking-tight">' + s.hora + '</span>' +
                        '<span class="slot__capacidad slot__agotado-text">' + T.sinDisp + '</span>';
                }

                $wrap.appendChild(btn);
            });

            if (oldHora) {
                var pre = $wrap.querySelector('[data-slot="' + oldHora + '"]');
                if (pre && !pre.disabled) marcar(pre);
            }
        }

        async function cargar() {
            var fecha = $fecha.value;
            if (!fecha) {
                $rangoInfo.textContent = '';
                $empty.textContent = @json(__('Elegí primero una fecha.'));
                $empty.style.display = '';
                $wrap.querySelectorAll('.slot').forEach(function (el) { el.remove(); });
                ocultarLoading();
                return;
            }

            var dow = new Date(fecha + 'T00:00:00').getDay();
            $rangoInfo.textContent = '\u00b7 ' + (dowLabels[dow] || T.cerrado);

            mostrarLoading();

            try {
                var url = '/api/horarios?fecha=' + encodeURIComponent(fecha) + '&personas=' + encodeURIComponent(personas);
                var res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                var data = await res.json();
                renderSlots(data.slots || []);
            } catch (_) {
                ocultarLoading();
                renderSlots([]);
            }
        }

        $fecha.addEventListener('change', cargar);
        cargar();

        var $form = document.getElementById('form-reserva');
        var $btnConfirmar = document.getElementById('btn-confirmar');
        var $btnText = document.getElementById('btn-text');
        var $btnSpinner = document.getElementById('btn-spinner');
        var $btnMisReservas = document.getElementById('btn-mis-reservas');
        var enviado = false;

        $form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (enviado) return;
            if (!$hidden.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Elegí un horario',
                    confirmButtonColor: '#d4a35c',
                    background: '#1a1f1d',
                    color: '#e8e0d4',
                });
                return;
            }

            enviado = true;
            $btnConfirmar.disabled = true;
            $btnConfirmar.classList.add('opacity-60', 'cursor-not-allowed');
            $btnText.classList.add('hidden');
            $btnSpinner.classList.remove('hidden');
            $btnSpinner.classList.add('inline-flex');
            $btnMisReservas.classList.add('pointer-events-none', 'opacity-40');

            var formData = new FormData($form);

            fetch($form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(function (res) {
                if (res.redirected) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Reserva confirmada!',
                        text: 'Te enviamos los detalles por mail.',
                        confirmButtonColor: '#d4a35c',
                        background: '#1a1f1d',
                        color: '#e8e0d4',
                        timer: 3000,
                        timerProgressBar: true,
                    }).then(function () {
                        window.location.href = res.url;
                    });
                    return;
                }
                return res.json();
            })
            .then(function (data) {
                if (!data) return;
                if (data.errors) {
                    var msgs = Object.values(data.errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Revisá los datos',
                        html: msgs,
                        confirmButtonColor: '#d4a35c',
                        background: '#1a1f1d',
                        color: '#e8e0d4',
                    });
                    resetBoton();
                } else if (data.message) {
                    var esExito = data.message.includes('creada');
                    Swal.fire({
                        icon: esExito ? 'success' : 'error',
                        title: data.message,
                        confirmButtonColor: '#d4a35c',
                        background: '#1a1f1d',
                        color: '#e8e0d4',
                        timer: esExito ? 3000 : undefined,
                        timerProgressBar: esExito,
                    }).then(function () {
                        if (esExito) window.location.href = '{{ route("reservas.mis-reservas") }}';
                        else resetBoton();
                    });
                }
            })
            .catch(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Reintentá en unos segundos.',
                    confirmButtonColor: '#d4a35c',
                    background: '#1a1f1d',
                    color: '#e8e0d4',
                });
                resetBoton();
            });

            function resetBoton() {
                enviado = false;
                $btnConfirmar.disabled = false;
                $btnConfirmar.classList.remove('opacity-60', 'cursor-not-allowed');
                $btnText.classList.remove('hidden');
                $btnSpinner.classList.add('hidden');
                $btnSpinner.classList.remove('inline-flex');
                $btnMisReservas.classList.remove('pointer-events-none', 'opacity-40');
            }
        });
    })();
    </script>
</x-app-layout>
