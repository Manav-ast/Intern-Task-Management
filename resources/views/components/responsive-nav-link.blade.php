@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-indigo-700 bg-indigo-50 text-base font-medium'
            : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
