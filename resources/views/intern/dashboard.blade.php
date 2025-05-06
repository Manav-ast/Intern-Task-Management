@extends('layouts.intern')

@section('content')
    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Welcome back, {{ auth('intern')->user()->name }}!
                </h1>
                <p class="mt-2 text-gray-600">Here's an overview of your tasks and activities.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <!-- Total Tasks Card -->
                <x-intern.stat-card :value="auth('intern')->user()->tasks->count()" label="Total Tasks" bg-color="bg-indigo-100" text-color="text-indigo-600"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />' />

                <!-- Pending Tasks -->
                <x-intern.stat-card :value="auth('intern')->user()->tasks->where('status', '!=', 'completed')->count()" label="Pending Tasks" bg-color="bg-yellow-100"
                    text-color="text-yellow-600"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />

                <!-- Completed Tasks -->
                <x-intern.stat-card :value="auth('intern')->user()->tasks->where('status', 'completed')->count()" label="Completed Tasks" bg-color="bg-green-100"
                    text-color="text-green-600"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Recent Tasks -->
                <div class="lg:col-span-2 order-2 lg:order-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Recent Tasks</h2>
                                <a href="{{ route('intern.tasks.index') }}"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse(auth('intern')->user()->tasks()->latest()->take(5)->get() as $task)
                                <x-intern.task-card :task="$task" />
                            @empty
                                <div class="p-4 sm:p-6 text-center">
                                    <p class="text-gray-500">No tasks assigned yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="lg:col-span-1 order-1 lg:order-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-4 sm:p-6 border-b border-gray-100">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Profile</h2>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-14 h-14 sm:w-16 sm:h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-xl sm:text-2xl font-medium text-indigo-600">
                                            {{ substr(auth('intern')->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base sm:text-lg font-medium text-gray-900">
                                        {{ auth('intern')->user()->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ auth('intern')->user()->email }}</p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Intern
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Joined {{ auth('intern')->user()->created_at->format('M Y') }}
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('intern.profile.edit') }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
