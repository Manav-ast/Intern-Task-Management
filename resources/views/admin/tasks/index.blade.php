@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Tasks</h2>
                @can('create-tasks')
                    <a href="{{ route('admin.tasks.create') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create New Task
                    </a>
                @endcan
            </div>

            @if ($tasks->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8 text-center border border-gray-100">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <p class="text-xl font-medium text-gray-900 mb-2">No tasks yet</p>
                        <p class="text-gray-500 mb-6">Get started by creating your first task.</p>
                        @can('create-tasks')
                            <a href="{{ route('admin.tasks.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Task
                            </a>
                        @endcan
                    </div>
                </div>
            @else
                <!-- Mobile View: Card Layout -->
                <div class="block sm:hidden space-y-4">
                    @foreach ($tasks as $task)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-medium text-gray-900">{{ $task->title }}</h3>
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $task->status === 'completed'
                                        ? 'bg-green-100 text-green-800'
                                        : ($task->status === 'in_progress'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        @foreach ($task->interns->take(3) as $intern)
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white">
                                                <span
                                                    class="text-xs font-medium text-indigo-700">{{ substr($intern->name, 0, 1) }}</span>
                                            </div>
                                        @endforeach
                                        @if ($task->interns->count() > 3)
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center border-2 border-white">
                                                <span
                                                    class="text-xs font-medium text-gray-600">+{{ $task->interns->count() - 3 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        {{ $task->comments->count() }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                @can('view-tasks')
                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-900">View Details</a>
                                @endcan
                                <div class="flex items-center space-x-3">
                                    @can('edit-tasks')
                                        <a href="{{ route('admin.tasks.edit', $task) }}"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Edit</a>
                                    @endcan
                                    @can('delete-tasks')
                                        <button type="button"
                                            onclick="deleteTask({{ $task->id }}, '{{ $task->title }}')"
                                            class="text-sm font-medium text-red-600 hover:text-red-900">Delete</button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop View: Table Layout -->
                <div class="hidden sm:block bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Due Date</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assigned Interns</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Comments</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($tasks as $task)
                                    <tr id="task-row-{{ $task->id }}"
                                        class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 task-title">{{ $task->title }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $task->status === 'completed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($task->status === 'in_progress'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex -space-x-2">
                                                @foreach ($task->interns->take(3) as $intern)
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white">
                                                        <span
                                                            class="text-xs font-medium text-indigo-700">{{ substr($intern->name, 0, 1) }}</span>
                                                    </div>
                                                @endforeach
                                                @if ($task->interns->count() > 3)
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center border-2 border-white">
                                                        <span
                                                            class="text-xs font-medium text-gray-600">+{{ $task->interns->count() - 3 }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $task->comments->count() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                @can('view-tasks')
                                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">View</a>
                                                @endcan
                                                @can('edit-tasks')
                                                    <a href="{{ route('admin.tasks.edit', $task) }}"
                                                        class="text-gray-600 hover:text-gray-900">Edit</a>
                                                @endcan
                                                @can('delete-tasks')
                                                    <button type="button"
                                                        onclick="deleteTask({{ $task->id }}, '{{ $task->title }}')"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Show success message if exists
                @if (session('success'))
                    Swal.fire({
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                @endif

                // Show error message if exists
                @if (session('error'))
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                @endif
            });

            function deleteTask(taskId, taskTitle) {
                Swal.fire({
                    title: 'Delete Task',
                    text: `Are you sure you want to delete "${taskTitle}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/tasks/${taskId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) {
                                // Remove the row with animation
                                $(`#task-row-${taskId}`).fadeOut('slow', function() {
                                    $(this).remove();
                                });

                                // Show success message
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message || 'Task deleted successfully',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Check if there are no more tasks and reload the page
                                    if ($('.task-title').length === 0) {
                                        window.location.reload();
                                    }
                                });
                            },
                            error: function(xhr) {
                                // Show error message
                                let errorMessage = 'Error deleting task. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
