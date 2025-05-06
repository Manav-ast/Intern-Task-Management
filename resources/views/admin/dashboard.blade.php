@extends('layouts.admin') {{-- Create this layout separately below --}}

@section('content')
    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 break-words">Welcome back,
                    {{ auth('admin')->user()->name }}!</h1>
                <p class="mt-2 text-sm sm:text-base text-gray-600">Here's an overview of your system's status.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <x-admin.stats-card :count="$internsCount ?? 0" label="Total Interns" bg-color="bg-indigo-100"
                    text-color="text-indigo-600" icon-name="interns" />

                <x-admin.stats-card :count="$activeTasks ?? 0" label="Active Tasks" bg-color="bg-green-100"
                    text-color="text-green-600" icon-name="tasks" />

                <x-admin.stats-card :count="$commentsCount ?? 0" label="Total Comments" bg-color="bg-purple-100"
                    text-color="text-purple-600" icon-name="comments" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">
                <!-- Recent Tasks -->
                <div class="lg:col-span-2">
                    <x-admin.recent-tasks :recentTasks="$recentTasks ?? []" />
                </div>

                <!-- Quick Actions & Profile -->
                <div class="lg:col-span-1 space-y-4 sm:space-y-8">
                    <x-admin.quick-actions />
                    <x-admin.profile-card :user="auth('admin')->user()" />
                </div>
            </div>
        </div>
    </div>
@endsection
