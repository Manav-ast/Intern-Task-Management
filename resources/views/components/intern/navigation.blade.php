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
            <!-- Chat notification badge will be injected here by JS -->
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
            <!-- Chat notification badge will be injected here by JS -->
        </x-responsive-nav-link>
    </div>
@endif

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
