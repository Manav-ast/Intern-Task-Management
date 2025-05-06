@props(['href', 'active', 'mobile' => false])

@php
    $baseClasses = $mobile
        ? 'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
        : 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium';

    $activeClasses = $mobile ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-indigo-500 text-gray-900';

    $inactiveClasses = $mobile
        ? 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700'
        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700';

    $classes = $active ? $activeClasses : $inactiveClasses;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $classes]) }}>
    {{ $slot }}
</a>
