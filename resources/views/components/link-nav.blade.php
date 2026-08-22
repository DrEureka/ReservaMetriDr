@props(['active'])

@if ($active ?? false)
    <a {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1.5 rounded text-sm font-medium text-brass transition-colors']) }}>
        {{ $slot }}
    </a>
@else
    <a {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1.5 rounded text-sm transition-colors']) }}
        :class="dark ? 'text-bone-300 hover:text-bone-100 hover:bg-ink-800' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100'">
        {{ $slot }}
    </a>
@endif
