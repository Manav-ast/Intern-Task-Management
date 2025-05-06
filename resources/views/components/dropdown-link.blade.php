@props(['active' => false])

@php
    $classes = 'block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
