@extends('layouts.admin')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.tasks.index') }}"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Tasks
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-4 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:space-x-4 w-full sm:w-auto">
                            <span
                                class="px-4 py-2 text-sm font-semibold rounded-full w-full sm:w-auto text-center
                                {{ $task->status === 'completed'
                                    ? 'bg-green-100 text-green-800'
                                    : ($task->status === 'in_progress'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <a href="{{ route('admin.tasks.edit', $task) }}"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                    Edit Task
                                </a>
                                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST"
                                    class="flex-1 sm:flex-none" id="delete-task-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDelete('Delete Task', 'Are you sure you want to delete this task? This action cannot be undone.', () => document.getElementById('delete-task-form').submit())"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="prose max-w-none mb-8 text-gray-600">
                        {{ $task->description }}
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">
                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                            <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium mr-2">Due Date:</span>
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                        </div>

                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                            <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="font-medium mr-2">Assigned Interns:</span>
                            {{ $task->interns->count() }}
                        </div>
                    </div>

                    <!-- Assigned Interns Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Interns</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($task->interns as $intern)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span
                                                    class="text-indigo-700 font-medium">{{ substr($intern->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $intern->name }}</p>
                                            <p class="text-sm text-gray-500 truncate">{{ $intern->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-4 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Comments</h2>
                        <span class="text-sm text-gray-500">{{ $task->comments->count() }}
                            {{ Str::plural('comment', $task->comments->count()) }}</span>
                    </div>

                    <!-- Error Alert -->
                    <div id="error-alert"
                        class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"></div>

                    <!-- Success Alert -->
                    <div id="success-alert"
                        class="hidden mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg"></div>

                    <form id="comment-form" class="mb-8 bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-100">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Add a comment</label>
                            <textarea id="message" name="message" rows="3"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200 p-4"
                                placeholder="Write your thoughts..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="submit-comment"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Post Comment</span>
                            </button>
                        </div>
                    </form>

                    <div class="space-y-4 sm:space-y-6" id="comments-list">
                        @foreach ($task->comments()->with('commentable')->latest()->get() as $comment)
                            <div class="comment-item bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-100"
                                data-comment-id="{{ $comment->id }}">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span
                                                class="text-indigo-700 font-medium">{{ substr($comment->commentable->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                                            <p class="text-sm font-medium text-gray-900">{{ $comment->commentable->name }}
                                            </p>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm text-gray-500">
                                                    {{ $comment->created_at->diffForHumans() }}</p>
                                                <form
                                                    action="{{ route('admin.tasks.comments.destroy', [$task, $comment]) }}"
                                                    method="POST" class="inline"
                                                    id="delete-comment-form-{{ $comment->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="confirmDelete('Delete Comment', 'Are you sure you want to delete this comment? This action cannot be undone.', () => document.getElementById('delete-comment-form-{{ $comment->id }}').submit())"
                                                        class="text-red-600 hover:text-red-900">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-gray-700 break-words">{{ $comment->message }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('comment-form');
                const commentsList = document.getElementById('comments-list');
                const messageInput = document.getElementById('message');
                const submitButton = document.getElementById('submit-comment');
                const errorAlert = document.getElementById('error-alert');
                const successAlert = document.getElementById('success-alert');

                function showAlert(element, message, duration = 3000) {
                    element.textContent = message;
                    element.classList.remove('hidden');
                    setTimeout(() => {
                        element.classList.add('hidden');
                    }, duration);
                }

                function setLoading(isLoading) {
                    submitButton.disabled = isLoading;
                    submitButton.querySelector('span').textContent = isLoading ? 'Posting...' : 'Post Comment';
                }

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const message = messageInput.value.trim();
                    if (!message) {
                        showAlert(errorAlert, 'Please enter a comment message.');
                        return;
                    }

                    setLoading(true);
                    errorAlert.classList.add('hidden');
                    successAlert.classList.add('hidden');

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const response = await fetch('{{ route('admin.tasks.comments.store', $task) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                message
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Error posting comment');
                        }

                        const commentHtml = `
                            <div class="comment-item bg-gray-50 p-4 sm:p-6 rounded-lg border border-gray-100" data-comment-id="${data.id}">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-700 font-medium">${data.author.charAt(0)}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                                            <p class="text-sm font-medium text-gray-900">${data.author}</p>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm text-gray-500">${data.created_at}</p>
                                                <form action="/admin/tasks/${data.task_id}/comments/${data.id}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?')"
                                                        class="text-red-600 hover:text-red-900">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-gray-700 break-words">${data.message}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        commentsList.insertAdjacentHTML('afterbegin', commentHtml);
                        messageInput.value = '';
                        showAlert(successAlert, 'Comment posted successfully!');
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert(errorAlert, error.message || 'Error posting comment. Please try again.');
                    } finally {
                        setLoading(false);
                    }
                });
            });
        </script>
    @endpush
@endsection
