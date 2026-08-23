<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="darkMode()" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F1614">

    <title>{{ $titulo ?? 'ReservaMetriDr' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        function darkMode() {
            return {
                dark: localStorage.getItem('theme') === 'light' ? false
                    : localStorage.getItem('theme') === 'dark' ? true
                    : window.matchMedia('(prefers-color-scheme: dark)').matches,
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased" :class="dark ? 'bg-ink-900 text-bone-100' : 'bg-stone-50 text-stone-900'">
    <div class="min-h-screen flex flex-col">

        {{-- Top bar minimal con brand + lang + toggle --}}
        <div class="border-b" :class="dark ? 'border-ink-600' : 'border-stone-200'">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-14">
                <a href="{{ route('reservas.create') }}" class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-brass text-ink-900 font-display font-semibold text-lg leading-none">r</span>
                    <span class="font-display text-base tracking-tight" :class="dark ? 'text-bone-100' : 'text-stone-900'">ReservaMetriDr</span>
                </a>
                <div class="flex items-center gap-3">
                    <button @click="toggle()" class="p-1.5 rounded-md transition-colors"
                        :class="dark ? 'text-bone-400 hover:text-bone-200 hover:bg-ink-800' : 'text-stone-500 hover:text-stone-700 hover:bg-stone-200'">
                        <svg x-show="dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg x-show="!dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-1 text-[11px] font-mono" x-data="{ lang: '{{ app()->getLocale() }}' }">
                        <a href="{{ url()->current() }}?lang=es"
                            class="px-1.5 py-0.5 rounded"
                            :class="lang === 'es' ? 'bg-brass text-ink-900 font-bold' : (dark ? 'text-bone-400 hover:text-bone-200' : 'text-stone-500 hover:text-stone-700')">es</a>
                        <a href="{{ url()->current() }}?lang=en"
                            class="px-1.5 py-0.5 rounded"
                            :class="lang === 'en' ? 'bg-brass text-ink-900 font-bold' : (dark ? 'text-bone-400 hover:text-bone-200' : 'text-stone-500 hover:text-stone-700')">en</a>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1 flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8">
                    <a href="{{ route('reservas.create') }}" class="inline-flex items-center justify-center w-12 h-12 rounded-md bg-brass text-ink-900 font-display font-semibold text-2xl leading-none">
                        r
                    </a>
                </div>

                <div class="tarjeta p-6 sm:p-8">
                    {{ $slot }}
                </div>

                <p class="text-center mt-6 text-[11px] font-mono uppercase tracking-[0.18em]"
                    :class="dark ? 'text-bone-400' : 'text-stone-500'">
                    <a href="{{ route('reservas.create') }}" class="hover:text-brass">
                        {{ __('Hacé tu reserva') }}
                    </a>
                </p>
            </div>
        </main>
    </div>
</body>
</html>