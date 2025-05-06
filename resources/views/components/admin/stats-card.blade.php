@props(['iconName', 'bgColor', 'textColor', 'count', 'label'])

<div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
    <div class="flex items-center">
        <div class="flex-shrink-0 {{ $bgColor }} rounded-lg p-2 sm:p-3">
            <div class="{{ $textColor }}">
                <x-admin.icons.stats-icon :name="$iconName" />
            </div>
        </div>
        <div class="ml-4">
            <h2 class="text-base sm:text-lg font-semibold text-gray-900">{{ $count }}</h2>
            <p class="text-xs sm:text-sm text-gray-500">{{ $label }}</p>
        </div>
    </div>
</div>
