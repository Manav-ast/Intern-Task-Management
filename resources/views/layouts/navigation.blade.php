<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-gray-800">Admin Panel</span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.interns.index') }}"
                        class="{{ request()->routeIs('admin.interns.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Manage Interns
                    </a>
                    <a href="{{ route('admin.tasks.index') }}"
                        class="{{ request()->routeIs('admin.tasks.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Manage Tasks
                    </a>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                        Logout
                    </button>
                </form>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button type="button" onclick="toggleMobileMenu()"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 border-indigo-500 text-indigo-700 border-l-4' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} block pl-3 pr-4 py-2 text-base font-medium">
                Dashboard
            </a>
            <a href="{{ route('admin.interns.index') }}"
                class="{{ request()->routeIs('admin.interns.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700 border-l-4' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} block pl-3 pr-4 py-2 text-base font-medium">
                Manage Interns
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="block">
                @csrf
                <button type="submit"
                    class="text-gray-500 hover:bg-gray-50 hover:text-gray-700 block w-full text-left pl-3 pr-4 py-2 text-base font-medium">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }
</script>
