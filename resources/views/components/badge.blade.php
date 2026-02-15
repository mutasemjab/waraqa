@props([
    'color' => 'primary',
])

<span {{ $attributes->merge(['class' => 'badge badge-' . $color]) }}>
    {{ $slot }}
</span>
