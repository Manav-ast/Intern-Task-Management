<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (auth()->check())
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-type" content="{{ get_class(auth()->user()) }}">
        <meta name="user-name" content="{{ auth()->user()->name }}">
        @if (isset($otherUser))
            <meta name="other-user-id" content="{{ $otherUser->id }}">
        @endif
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-minimal@5/minimal.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .modal-backdrop {
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('intern.dashboard') }}" class="text-xl font-bold text-indigo-600">
                                {{ config('app.name', 'Laravel') }}
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <x-intern.navigation :mobile="false" />
                    </div>

                    <!-- Desktop Profile Dropdown -->
                    <x-intern.profile-dropdown :mobile="false" />

                    <!-- Mobile Menu Button -->
                    <x-mobile-menu-button />
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="sm:hidden" x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95">

                <!-- Mobile Navigation -->
                <x-intern.navigation :mobile="true" />

                <!-- Mobile Profile Menu -->
                <x-intern.profile-dropdown :mobile="true" />
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Stack for additional scripts -->
    @stack('scripts')

    <!-- Alert Component -->
    <x-alert />
</body>

</html>
