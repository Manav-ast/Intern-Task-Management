@props(['icon', 'value', 'label', 'bgColor' => 'bg-indigo-100', 'textColor' => 'text-indigo-600'])

<div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
    <div class="flex items-center">
        <div class="flex-shrink-0 {{ $bgColor }} rounded-lg p-3">
            <svg class="w-6 h-6 {{ $textColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        </div>
        <div class="ml-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ $value }}</h2>
            <p class="text-sm text-gray-500">{{ $label }}</p>
        </div>
    </div>
</div>
