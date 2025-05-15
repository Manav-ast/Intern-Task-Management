@extends('layouts.admin')

@section('content')
    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">{{ __('Edit Admin') }}</h2>
                    <form id="editAdminForm" action="{{ route('admin.admins.update', $admin) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4 sm:space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}"
                                    class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', $admin->email) }}"
                                    class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base"
                                    disabled>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password (leave
                                    blank
                                    to keep current)</label>
                                <input type="password" name="password" id="password"
                                    class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-1">Confirm
                                    Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                                <div class="mt-2 space-y-3 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-3">
                                    @foreach ($roles as $role)
                                        <div
                                            class="relative flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                            <div class="flex items-center h-6">
                                                <input type="radio" name="roles[]" value="{{ $role->id }}"
                                                    id="role_{{ $role->id }}"
                                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    {{ in_array($role->id, old('roles', $admin->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 flex-grow">
                                                <label for="role_{{ $role->id }}"
                                                    class="text-sm sm:text-base font-medium text-gray-700 cursor-pointer block py-1">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="mt-6 sm:mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('admin.admins.index') }}"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .error {
                color: #DC2626;
                font-size: 0.875rem;
                margin-top: 0.25rem;
                display: block;
            }

            input.error {
                border-color: #DC2626 !important;
                background-color: #FEF2F2 !important;
            }

            input.valid {
                border-color: #10B981 !important;
                background-color: #ECFDF5 !important;
            }

            @media (max-width: 640px) {
                .error {
                    font-size: 0.75rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                $("#editAdminForm").validate({
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
                            minlength: {
                                param: 8,
                                depends: function(element) {
                                    return $(element).val().length > 0;
                                }
                            }
                        },
                        password_confirmation: {
                            equalTo: {
                                param: "#password",
                                depends: function(element) {
                                    return $("#password").val().length > 0;
                                }
                            }
                        },
                        'roles[]': {
                            required: true,
                            minlength: 1
                        }
                    },
                    messages: {
                        name: {
                            required: "Please enter the admin's name",
                            minlength: "Name must be at least 3 characters long"
                        },
                        email: {
                            required: "Please enter an email address",
                            email: "Please enter a valid email address"
                        },
                        password: {
                            minlength: "Password must be at least 8 characters long"
                        },
                        password_confirmation: {
                            equalTo: "Passwords do not match"
                        },
                        'roles[]': {
                            required: "Please select at least one role",
                            minlength: "Please select at least one role"
                        }
                    },
                    errorElement: 'span',
                    errorClass: 'error',
                    validClass: 'valid',
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('error').removeClass('valid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('error').addClass('valid');
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr("type") === "checkbox") {
                            error.insertAfter(element.closest('.space-y-2'));
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    submitHandler: function(form) {
                        const submitBtn = $(form).find('button[type="submit"]');
                        const originalText = submitBtn.html();
                        submitBtn.prop('disabled', true)
                            .html(
                                '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Updating...'
                            );
                        form.submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
