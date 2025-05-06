@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Add New Intern</h2>
                    <p class="mt-1 text-sm text-gray-500">Create a new intern account in the system.</p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('admin.interns.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-8">
                    <form method="POST" action="{{ route('admin.interns.store') }}" class="space-y-8"
                        id="createInternForm">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }}
                                            errors
                                            with your submission</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-8">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <input type="text" name="name" id="name"
                                        class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200 @error('name') border-red-300 @enderror"
                                        value="{{ old('name') }}">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <input type="email" name="email" id="email"
                                        class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200 @error('email') border-red-300 @enderror"
                                        value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <input type="password" name="password" id="password"
                                        class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200 @error('password') border-red-300 @enderror">
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2">Confirm
                                    Password</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <button type="button" onclick="window.location.href='{{ route('admin.interns.index') }}'"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create Intern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .error-field {
            border-color: #EF4444 !important;
            background-color: #FEF2F2 !important;
        }

        .valid-field {
            border-color: #10B981 !important;
            background-color: #ECFDF5 !important;
        }

        .error-message {
            color: #DC2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        input:focus {
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#createInternForm").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    name: {
                        required: "Please enter the intern's full name",
                        minlength: "Name must be at least 3 characters long"
                    },
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    password: {
                        required: "Please provide a password",
                        minlength: "Password must be at least 8 characters long"
                    },
                    password_confirmation: {
                        required: "Please confirm the password",
                        equalTo: "Passwords do not match"
                    }
                },
                errorElement: 'p',
                errorClass: 'error-message',
                validClass: 'valid-field',
                submitHandler: function(form) {
                    const submitBtn = $(form).find('button[type="submit"]');
                    const originalText = submitBtn.html();
                    submitBtn.html(
                        '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating...'
                    ).prop('disabled', true);

                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Intern created successfully',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href =
                                    '{{ route('admin.interns.index') }}';
                            });
                        },
                        error: function(xhr) {
                            submitBtn.html(originalText).prop('disabled', false);
                            let errorMessage = 'Failed to create intern';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
