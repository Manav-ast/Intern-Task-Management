@extends('layouts.admin') {{-- Create this layout separately below --}}

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth('admin')->user()->name }}!</h1>
                <p class="mt-2 text-gray-600">Here's an overview of your system's status.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Interns Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $internsCount ?? 0 }}</h2>
                            <p class="text-sm text-gray-500">Total Interns</p>
                        </div>
                    </div>
                </div>

                <!-- Active Tasks -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $activeTasks ?? 0 }}</h2>
                            <p class="text-sm text-gray-500">Active Tasks</p>
                        </div>
                    </div>
                </div>

                <!-- Total Comments -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $commentsCount ?? 0 }}</h2>
                            <p class="text-sm text-gray-500">Total Comments</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Tasks -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">Recent Tasks</h2>
                                <a href="{{ route('admin.tasks.index') }}"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($recentTasks ?? [] as $task)
                                <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</h3>
                                            <div class="mt-1 flex items-center space-x-4">
                                                <span class="text-sm text-gray-500">
                                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $task->status === 'completed'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($task->status === 'in_progress'
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </div>
                                            <div class="mt-2 flex items-center">
                                                <div class="flex -space-x-2">
                                                    @foreach ($task->interns->take(3) as $intern)
                                                        <div
                                                            class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white">
                                                            <span
                                                                class="text-xs font-medium text-indigo-700">{{ substr($intern->name, 0, 1) }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if ($task->interns->count() > 3)
                                                        <div
                                                            class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center border-2 border-white">
                                                            <span
                                                                class="text-xs font-medium text-gray-600">+{{ $task->interns->count() - 3 }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="ml-2 text-sm text-gray-500">{{ $task->interns->count() }}
                                                    {{ Str::plural('intern', $task->interns->count()) }} assigned</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.tasks.show', $task) }}" class="ml-4 flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center">
                                    <p class="text-gray-500">No tasks created yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Profile -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <a href="{{ route('admin.tasks.create') }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Create New Task
                            </a>
                            <a href="{{ route('admin.interns.create') }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Add New Intern
                            </a>
                        </div>
                    </div>

                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-xl font-semibold text-gray-900">Profile</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-2xl font-medium text-indigo-600">
                                            {{ substr(auth('admin')->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ auth('admin')->user()->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ auth('admin')->user()->email }}</p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Administrator
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Joined {{ auth('admin')->user()->created_at->format('M Y') }}
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="button"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
