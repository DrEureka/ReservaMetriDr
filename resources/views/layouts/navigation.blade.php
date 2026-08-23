<nav x-data="{ abierto: false }" class="backdrop-blur border-b sticky top-0 z-30" :class="dark ? 'bg-ink-900/80 border-ink-600' : 'bg-white/80 border-stone-200'">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">

            {{-- Brand --}}
            <a href="{{ route('reservas.create') }}" class="flex items-center gap-2.5 group">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-brass text-ink-900 font-display font-semibold text-lg leading-none">
                    r
                </span>
                <span class="font-display text-base sm:text-lg tracking-tight group-hover:opacity-80"
                    :class="dark ? 'text-bone-100' : 'text-stone-900'">
                    ReservaMetriDr
                </span>
            </a>

            {{-- Center nav (desktop) --}}
            <div class="hidden sm:flex items-center gap-6">
                <x-link-nav :href="route('reservas.create')" :active="request()->routeIs('reservas.create')">
                    {{ __('Nueva reserva') }}
                </x-link-nav>
                <x-link-nav :href="route('reservas.mis-reservas')" :active="request()->routeIs('reservas.mis-reservas')">
                    {{ __('Mis reservas') }}
                </x-link-nav>
                @auth
                    @if (Auth::user()->esAdmin())
                        <x-link-nav :href="route('admin.listado.index')" :active="request()->routeIs('admin.listado.*')">
                            {{ __('Listado') }}
                        </x-link-nav>
                        <x-link-nav :href="route('admin.mesas.index')" :active="request()->routeIs('admin.mesas.*')">
                            {{ __('Mesas') }}
                        </x-link-nav>
                    @endif
                @endauth
            </div>

            {{-- Right: theme toggle + lang switcher + auth --}}
            <div class="hidden sm:flex items-center gap-3">
                {{-- Theme toggle --}}
                <button @click="toggle()" class="p-1.5 rounded-md transition-colors"
                    :class="dark ? 'text-bone-400 hover:text-bone-200 hover:bg-ink-800' : 'text-stone-500 hover:text-stone-700 hover:bg-stone-200'"
                    :aria-label="dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    :title="dark ? 'Modo claro' : 'Modo oscuro'">
                    <svg x-show="dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <div class="w-px h-4" :class="dark ? 'bg-ink-600' : 'bg-stone-300'"></div>

                {{-- Lang switcher --}}
                <div class="flex items-center gap-1 text-[11px] font-mono" x-data="{ lang: '{{ app()->getLocale() }}' }">
                    <a href="{{ url()->current() }}?lang=es"
                        class="px-1.5 py-0.5 rounded"
                        :class="lang === 'es' ? 'bg-brass text-ink-900 font-bold' : (dark ? 'text-bone-400 hover:text-bone-200' : 'text-stone-500 hover:text-stone-700')">es</a>
                    <a href="{{ url()->current() }}?lang=en"
                        class="px-1.5 py-0.5 rounded"
                        :class="lang === 'en' ? 'bg-brass text-ink-900 font-bold' : (dark ? 'text-bone-400 hover:text-bone-200' : 'text-stone-500 hover:text-stone-700')">en</a>
                </div>

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs uppercase tracking-wider"
                            :class="dark ? 'text-bone-400 hover:text-bone-100' : 'text-stone-500 hover:text-stone-900'">
                            {{ __('Salir') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs uppercase tracking-wider"
                        :class="dark ? 'text-bone-300 hover:text-bone-100' : 'text-stone-600 hover:text-stone-900'">
                        {{ __('Entrar') }}
                    </a>
                    <a href="{{ route('register') }}" class="text-xs uppercase tracking-wider px-3 py-1.5 rounded border transition-colors duration-150 border-brass text-brass hover:bg-brass hover:text-ink-900">
                        {{ __('Registro') }}
                    </a>
                @endauth
            </div>

            {{-- Mobile: theme toggle + lang + hamburger --}}
            <div class="flex sm:hidden items-center gap-2">
                <button @click="toggle()" class="p-1.5 rounded-md transition-colors"
                    :class="dark ? 'text-bone-400 hover:text-bone-200' : 'text-stone-500 hover:text-stone-700'"
                    :aria-label="dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">
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
                        :class="lang === 'es' ? 'bg-brass text-ink-900 font-bold' : (dark ? 'text-bone-400' : 'text-stone-500')">es</a>
                    <a href="{{ url()->current() }}?lang=en"
                        class="px-1.5 py-0.5 rounded"
                        :class="lang === 'en' ? 'bg-brass text-ink-900 font-bold' : (dark ? 'text-bone-400' : 'text-stone-500')">en</a>
                </div>
                <button @click="abierto = ! abierto"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-md focus:outline-none"
                    :class="dark ? 'text-bone-300 hover:text-bone-100 hover:bg-ink-800' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200'"
                    :aria-label="abierto ? 'Cerrar menú' : 'Abrir menú'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!abierto" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="abierto" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="abierto" x-collapse class="sm:hidden border-t" :class="dark ? 'border-ink-600' : 'border-stone-200'">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('reservas.create') }}"
                class="block px-3 py-2 rounded text-sm"
                x-data="{ activo: {{ Js::from(request()->routeIs('reservas.create')) }} }"
                :class="activo ? 'bg-brass text-ink-900 font-medium' : (dark ? 'text-bone-200 hover:bg-ink-800' : 'text-stone-700 hover:bg-stone-100')">
                {{ __('Nueva reserva') }}
            </a>
            <a href="{{ route('reservas.mis-reservas') }}"
                class="block px-3 py-2 rounded text-sm"
                x-data="{ activo: {{ Js::from(request()->routeIs('reservas.mis-reservas')) }} }"
                :class="activo ? 'bg-brass text-ink-900 font-medium' : (dark ? 'text-bone-200 hover:bg-ink-800' : 'text-stone-700 hover:bg-stone-100')">
                {{ __('Mis reservas') }}
            </a>
            @auth
                @if (Auth::user()->esAdmin())
                    <a href="{{ route('admin.listado.index') }}"
                        class="block px-3 py-2 rounded text-sm"
                        :class="dark ? 'text-bone-200 hover:bg-ink-800' : 'text-stone-700 hover:bg-stone-100'">
                        {{ __('Listado') }}
                    </a>
                    <a href="{{ route('admin.mesas.index') }}"
                        class="block px-3 py-2 rounded text-sm"
                        :class="dark ? 'text-bone-200 hover:bg-ink-800' : 'text-stone-700 hover:bg-stone-100'">
                        {{ __('Mesas') }}
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded text-sm"
                        :class="dark ? 'text-bone-400 hover:bg-ink-800' : 'text-stone-500 hover:bg-stone-100'">
                        {{ __('Salir') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded text-sm"
                    :class="dark ? 'text-bone-200 hover:bg-ink-800' : 'text-stone-700 hover:bg-stone-100'">
                    {{ __('Entrar') }}
                </a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded text-sm"
                    :class="dark ? 'text-bone-200 hover:bg-ink-800' : 'text-stone-700 hover:bg-stone-100'">
                    {{ __('Registro') }}
                </a>
            @endauth
        </div>
    </div>
</nav>