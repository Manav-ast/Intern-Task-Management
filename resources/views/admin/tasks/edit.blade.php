@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto py-6">
        <h2 class="text-2xl font-semibold mb-4">Edit Task</h2>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.tasks.update', $task) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label for="title" class="block font-medium">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    @error('title')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block font-medium">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1">{{ old('description', $task->description) }}</textarea>
                </div>

                <div>
                    <label for="due_date" class="block font-medium">Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                        value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    @error('due_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block font-medium">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                        @foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $key => $label)
                            <option value="{{ $key }}" {{ $task->status == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="interns" class="block font-medium">Assigned Interns</label>
                    <select name="interns[]" id="interns" multiple
                        class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                        @foreach ($interns as $intern)
                            <option value="{{ $intern->id }}"
                                {{ in_array($intern->id, $selectedInterns) ? 'selected' : '' }}>
                                {{ $intern->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('interns')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update
                        Task</button>
                </div>
            </form>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Comments</h3>

            <div class="mb-4">
                <form id="comment-form" class="space-y-4">
                    <div>
                        <label for="message" class="block font-medium">Add a Comment</label>
                        <textarea name="message" id="message" rows="3"
                            class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring"></textarea>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Post Comment
                    </button>
                </form>
            </div>

            <div id="comments-list" class="space-y-4">
                @foreach ($task->comments()->with('commentable')->latest()->get() as $comment)
                    <div class="comment-item bg-gray-50 p-4 rounded-lg" data-comment-id="{{ $comment->id }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $comment->commentable->name }}</p>
                                <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                            @if ($comment->commentable_id === auth('admin')->id())
                                <form action="{{ route('admin.tasks.comments.destroy', [$task, $comment]) }}"
                                    method="POST" class="delete-comment-form"
                                    onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <p class="mt-2 text-gray-700">{{ $comment->message }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#interns').select2({
                    placeholder: 'Select interns',
                    allowClear: true,
                    width: '100%'
                });

                // Handle comment submission
                $('#comment-form').on('submit', function(e) {
                    e.preventDefault();
                    const message = $('#message').val();
                    if (!message) return;

                    $.ajax({
                        url: '{{ route('admin.tasks.comments.store', $task) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            message
                        },
                        success: function(response) {
                            // Add new comment to the list
                            const commentHtml = `
                                <div class="comment-item bg-gray-50 p-4 rounded-lg" data-comment-id="${response.id}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">${response.author}</p>
                                            <p class="text-sm text-gray-500">${response.created_at}</p>
                                        </div>
                                        <form action="/admin/tasks/{{ $task->id }}/comments/${response.id}"
                                            method="POST"
                                            class="delete-comment-form"
                                            onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <p class="mt-2 text-gray-700">${response.message}</p>
                                </div>
                            `;
                            $('#comments-list').prepend(commentHtml);
                            $('#message').val('');
                        },
                        error: function(xhr) {
                            alert('Error posting comment. Please try again.');
                        }
                    });
                });

                // Handle comment deletion
                $(document).on('submit', '.delete-comment-form', function(e) {
                    e.preventDefault();
                    const form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            form.closest('.comment-item').fadeOut(function() {
                                $(this).remove();
                            });
                        },
                        error: function() {
                            alert('Error deleting comment. Please try again.');
                        }
                    });
                });
            });
        </script>
    @endpush

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush
@endsection
