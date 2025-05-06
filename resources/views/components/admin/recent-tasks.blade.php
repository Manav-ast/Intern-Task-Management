@props(['recentTasks'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Recent Tasks</h2>
            <a href="{{ route('admin.tasks.index') }}"
                class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                View All
            </a>
        </div>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($recentTasks ?? [] as $task)
            <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-start sm:items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</h3>
                        <div class="mt-1 flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                            <span class="text-xs sm:text-sm text-gray-500">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                            </span>
                            <span
                                class="mt-1 sm:mt-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
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
                                        class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white">
                                        <span
                                            class="text-xs font-medium text-indigo-700">{{ substr($intern->name, 0, 1) }}</span>
                                    </div>
                                @endforeach
                                @if ($task->interns->count() > 3)
                                    <div
                                        class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-gray-100 flex items-center justify-center border-2 border-white">
                                        <span
                                            class="text-xs font-medium text-gray-600">+{{ $task->interns->count() - 3 }}</span>
                                    </div>
                                @endif
                            </div>
                            <span class="ml-2 text-xs sm:text-sm text-gray-500">{{ $task->interns->count() }}
                                {{ Str::plural('intern', $task->interns->count()) }} assigned</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.tasks.show', $task) }}"
                        class="ml-4 flex-shrink-0 p-2 -m-2 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">View task</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-4 sm:p-6 text-center">
                <p class="text-sm text-gray-500">No tasks created yet</p>
            </div>
        @endforelse
    </div>
</div>
