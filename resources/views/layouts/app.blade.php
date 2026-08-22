<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="darkMode()" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F1614">

    <title>{{ $titulo ?? 'Reservá tu mesa' }} · ReservaMetriDr</title>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
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

        @include('layouts.navigation')

        @isset($encabezado)
            <header class="border-b" :class="dark ? 'border-ink-600' : 'border-stone-200'">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                    {{ $encabezado }}
                </div>
            </header>
        @endisset

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t mt-12" :class="dark ? 'border-ink-600' : 'border-stone-200'">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs flex items-center justify-between"
                 :class="dark ? 'text-bone-400' : 'text-stone-500'">
                <span>ReservaMetriDr</span>
                <span class="font-mono">© {{ date('Y') }}</span>
            </div>
        </footer>
    </div>

    @if (session('exito'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: '{{ session("exito") }}',
                    confirmButtonColor: '#d4a35c',
                    background: document.documentElement.classList.contains('dark') ? '#1a1f1d' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#e8e0d4' : '#1c1917',
                    timer: 3500,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    title: '{{ session("status") }}',
                    confirmButtonColor: '#d4a35c',
                    background: document.documentElement.classList.contains('dark') ? '#1a1f1d' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#e8e0d4' : '#1c1917',
                });
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Revisá los datos',
                    html: '{!! implode('<br>', $errors->all()) !!}',
                    confirmButtonColor: '#d4a35c',
                    background: document.documentElement.classList.contains('dark') ? '#1a1f1d' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#e8e0d4' : '#1c1917',
                });
            });
        </script>
    @endif
</body>
</html>