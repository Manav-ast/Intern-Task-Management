@props(['mobile' => false])

@if (!$mobile)
    <!-- Desktop Navigation Links -->
    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
        <x-nav-link href="{{ route('intern.dashboard') }}" :active="request()->routeIs('intern.dashboard')">
            Dashboard
        </x-nav-link>
        <x-nav-link href="{{ route('intern.tasks.index') }}" :active="request()->routeIs('intern.tasks.*')">
            Tasks
        </x-nav-link>
        <x-nav-link href="{{ route('intern.chat.index') }}" :active="request()->routeIs('intern.chat.*')">
            Chat
        </x-nav-link>
    </div>
@else
    <!-- Mobile Navigation Links -->
    <div class="pt-2 pb-3 space-y-1">
        <x-responsive-nav-link href="{{ route('intern.dashboard') }}" :active="request()->routeIs('intern.dashboard')">
            Dashboard
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('intern.tasks.index') }}" :active="request()->routeIs('intern.tasks.*')">
            Tasks
        </x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('intern.chat.index') }}" :active="request()->routeIs('intern.chat.*')">
            Chat
        </x-responsive-nav-link>
    </div>
@endif
