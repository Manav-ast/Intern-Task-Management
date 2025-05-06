@props(['user'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Profile</h2>
    </div>
    <div class="p-4 sm:p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                    <span class="text-xl sm:text-2xl font-medium text-indigo-600">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-base sm:text-lg font-medium text-gray-900 break-words">{{ $user->name }}</h3>
                <p class="text-xs sm:text-sm text-gray-500 break-words">{{ $user->email }}</p>
            </div>
        </div>

        <div class="mt-4 sm:mt-6 space-y-3 sm:space-y-4">
            <div class="flex items-center text-xs sm:text-sm text-gray-500">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Administrator
            </div>
            <div class="flex items-center text-xs sm:text-sm text-gray-500">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Joined {{ $user->created_at->format('M Y') }}
            </div>
        </div>

        <div class="mt-4 sm:mt-6">
            <button type="button"
                class="w-full inline-flex items-center justify-center px-4 py-2 sm:py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit Profile
            </button>
        </div>
    </div>
</div>
