@extends('layouts.intern')

@section('content')
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with task count -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">My Tasks</h2>
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    {{ $tasks->total() }} {{ Str::plural('task', $tasks->total()) }}
                </span>
            </div>

            @if ($tasks->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-gray-100">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <p class="text-lg font-medium text-gray-900 mb-1">No tasks yet</p>
                        <p class="text-gray-500">You don't have any tasks assigned to you at the moment.</p>
                    </div>
                </div>
            @else
                <div class="grid gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tasks as $task)
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:border-gray-200 transition-colors duration-200">
                            <div class="p-4 sm:p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base sm:text-lg font-medium text-gray-900 truncate mb-1">
                                            {{ $task->title }}
                                        </h3>
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
                                </div>

                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $task->description }}</p>

                                <div class="space-y-3">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Assigned by: {{ $task->admin->name }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        {{ $task->comments->count() }}
                                        {{ Str::plural('comment', $task->comments->count()) }}
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100">
                                <a href="{{ route('intern.tasks.show', $task) }}"
                                    class="w-full inline-flex items-center justify-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                    View Details
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6 sm:mt-8">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
