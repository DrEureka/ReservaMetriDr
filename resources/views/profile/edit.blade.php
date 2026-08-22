<x-app-layout>
    <x-slot name="encabezado">
        <p class="text-[11px] font-mono uppercase tracking-[0.2em] text-brass">{{ __('Cuenta') }}</p>
        <h1 class="font-display text-display-sm mt-2">{{ __('Mi perfil') }}</h1>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto space-y-6">
            <div class="tarjeta p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="tarjeta p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <div class="tarjeta p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>