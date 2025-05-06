@extends('layouts.admin')

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create New Task</h1>
                        <p class="mt-2 text-sm text-gray-600">Create a new task and assign it to interns.</p>
                    </div>
                    <a href="{{ route('admin.tasks.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Tasks
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <form action="{{ route('admin.tasks.store') }}" method="POST" class="divide-y divide-gray-200"
                    id="createTaskForm">
                    @csrf
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 p-4 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter task title">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 p-4 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter task description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 p-4 focus:border-indigo-500 sm:text-sm">
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 p-4 focus:border-indigo-500 sm:text-sm">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="interns" class="block text-sm font-medium text-gray-700">Assign to Interns</label>
                            <select name="interns[]" id="interns" multiple
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 p-4 focus:border-indigo-500 sm:text-sm">
                                @foreach ($interns as $intern)
                                    <option value="{{ $intern->id }}"
                                        {{ old('interns') && in_array($intern->id, old('interns')) ? 'selected' : '' }}>
                                        {{ $intern->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('interns')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.tasks.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#interns').select2({
                    theme: 'classic',
                    placeholder: 'Select interns',
                    allowClear: true,
                    width: '100%'
                });

                // Initialize form validation
                $('#createTaskForm').validate({
                    ignore: [], // Don't ignore hidden inputs (important for select2)
                    rules: {
                        title: {
                            required: true,
                            minlength: 3,
                            maxlength: 255
                        },
                        description: {
                            required: true,
                            minlength: 10
                        },
                        due_date: {
                            required: true,
                            date: true
                        },
                        status: {
                            required: true
                        },
                        'interns[]': {
                            required: true,
                            minlength: 1
                        }
                    },
                    messages: {
                        title: {
                            required: "Please enter a task title",
                            minlength: "Title must be at least 3 characters long",
                            maxlength: "Title cannot be longer than 255 characters"
                        },
                        description: {
                            required: "Please enter a task description",
                            minlength: "Description must be at least 10 characters long"
                        },
                        due_date: {
                            required: "Please select a due date",
                            date: "Please enter a valid date"
                        },
                        status: {
                            required: "Please select a status"
                        },
                        'interns[]': {
                            required: "Please assign at least one intern",
                            minlength: "Please assign at least one intern"
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        if (element.attr('id') === 'interns') {
                            error.insertAfter(element.next('.select2-container'));
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element) {
                        $(element).addClass('border-red-500').removeClass('border-gray-300');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('border-red-500').addClass('border-gray-300');
                    },
                    errorClass: 'text-red-600 text-sm mt-1',
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--classic .select2-selection--multiple {
                border-color: #D1D5DB !important;
                border-radius: 0.375rem !important;
            }

            .select2-container--classic .select2-selection--multiple:focus {
                border-color: #6366F1 !important;
                box-shadow: 0 0 0 1px #6366F1 !important;
            }



            /* Validation Styles */
            .error {
                color: #dc2626;
                font-size: 0.875rem;
                margin-top: 0.25rem;
                display: block;
            }

            input.error,
            textarea.error,
            select.error {
                border-color: #dc2626 !important;
            }

            .select2-container--classic .select2-selection--multiple.error {
                border-color: #dc2626 !important;
            }
        </style>
    @endpush
@endsection
