@php
    $navLinks = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.interns.index', 'label' => 'Manage Interns'],
        ['route' => 'admin.tasks.index', 'label' => 'Manage Tasks'],
        ['route' => 'admin.roles.index', 'label' => 'Manage Roles'],
        ['route' => 'admin.admins.index', 'label' => 'Manage Admins'],
        ['route' => 'admin.chat.index', 'label' => 'Chat'],
    ];
@endphp

<nav class="bg-white border-b border-gray-200" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-indigo-600">Admin Panel</span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    @foreach ($navLinks as $link)
                        <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['route'] . '*')">
                            {{ $link['label'] }}
                            @if ($link['route'] === 'admin.chat.index')
                                <!-- Chat notification badge will be injected here by JS -->
                            @endif
                        </x-nav-link>
                    @endforeach
                </div>
            </div>

            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <x-profile-dropdown />
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button type="button" @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" :class="{ 'hidden': open, 'inline-flex': !open }" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'inline-flex': open, 'hidden': !open }" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($navLinks as $link)
                <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['route'] . '*')" :mobile="true">
                    {{ $link['label'] }}
                    @if ($link['route'] === 'admin.chat.index')
                        <!-- Chat notification badge will be injected here by JS -->
                    @endif
                </x-nav-link>
            @endforeach
        </div>
        <x-profile-dropdown mobile />
    </div>
</nav>

<style>
    .chat-nav-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.25rem;
        height: 1.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        background-color: #4f46e5;
        border-radius: 9999px;
        margin-left: 0.5rem;
    }
</style>
