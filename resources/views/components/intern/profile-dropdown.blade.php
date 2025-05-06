@props(['mobile' => false])

@if (!$mobile)
    <!-- Desktop Profile Dropdown -->
    <div class="hidden sm:flex sm:items-center sm:ml-6">
        <div class="relative" x-data="{ profileOpen: false }" @click.away="profileOpen = false">
            <button @click="profileOpen = !profileOpen"
                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <span>{{ Auth::guard('intern')->user()->name }}</span>
                <svg class="ml-2 h-4 w-4" :class="{ 'rotate-180': profileOpen }" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="profileOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 w-48 mt-2 py-1 bg-white rounded-md shadow-lg z-50">
                <x-dropdown-link href="{{ route('intern.profile.edit') }}">
                    Profile
                </x-dropdown-link>
                <form method="POST" action="{{ route('intern.logout') }}">
                    @csrf
                    <x-dropdown-link href="{{ route('intern.logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Logout
                    </x-dropdown-link>
                </form>
            </div>
        </div>
    </div>
@else
    <!-- Mobile Profile Menu -->
    <div class="pt-4 pb-3 border-t border-gray-200">
        <x-intern.mobile-profile-info />
        <div class="mt-3 space-y-1">
            <x-responsive-nav-link href="{{ route('intern.profile.edit') }}">
                Profile
            </x-responsive-nav-link>
            <form method="POST" action="{{ route('intern.logout') }}">
                @csrf
                <x-responsive-nav-link href="{{ route('intern.logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    Logout
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
@endif
