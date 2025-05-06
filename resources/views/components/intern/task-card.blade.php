@props(['task'])

<div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-200">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</h3>
            <div class="mt-1 flex flex-wrap items-center gap-2 sm:gap-4">
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
        </div>
        <a href="{{ route('intern.tasks.show', $task) }}" class="ml-4 flex-shrink-0">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
