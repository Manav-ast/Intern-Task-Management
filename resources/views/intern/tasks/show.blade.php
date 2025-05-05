@extends('layouts.intern')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('intern.tasks.index') }}"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Tasks
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                        <span
                            class="px-4 py-2 text-sm font-semibold rounded-full transition-colors duration-200
                            {{ $task->status === 'completed'
                                ? 'bg-green-100 text-green-800'
                                : ($task->status === 'in_progress'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>

                    <div class="prose max-w-none mb-8 text-gray-600">
                        {{ $task->description }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium mr-2">Due Date:</span>
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                        </div>

                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="font-medium mr-2">Assigned by:</span>
                            {{ $task->admin->name }}
                        </div>
                    </div>

                    @if ($task->interns->count() > 1)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Also assigned to:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($task->interns as $intern)
                                    @if ($intern->id !== auth('intern')->id())
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $intern->name }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Comments</h2>

                    <!-- Error Alert -->
                    <div id="error-alert"
                        class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"></div>

                    <!-- Success Alert -->
                    <div id="success-alert"
                        class="hidden mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg"></div>

                    <form id="comment-form" class="mb-8">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Add a comment</label>
                            <textarea id="message" name="message" rows="3"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200 p-4"
                                placeholder="Write your thoughts..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="submit-comment"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Post Comment</span>
                            </button>
                        </div>
                    </form>

                    <div id="comments-list" class="space-y-4">
                        @foreach ($task->comments()->with('commentable')->latest()->get() as $comment)
                            <div class="comment-item bg-gray-50 p-6 rounded-lg border border-gray-100"
                                data-comment-id="{{ $comment->id }}">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span
                                                class="text-indigo-700 font-medium">{{ substr($comment->commentable->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $comment->commentable->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        <div class="mt-2 text-gray-700">{{ $comment->message }}</div>
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
                        const response = await fetch('{{ route('intern.tasks.comments.store', $task) }}', {
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
                            <div class="comment-item bg-gray-50 p-6 rounded-lg border border-gray-100" data-comment-id="${data.id}">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-700 font-medium">${data.author.charAt(0)}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">${data.author}</p>
                                        <p class="text-sm text-gray-500">${data.created_at}</p>
                                        <div class="mt-2 text-gray-700">${data.message}</div>
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
