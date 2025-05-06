@extends('layouts.admin')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Edit Role') }}</h2>
                    <form id="editRoleForm" action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                                class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $role->slug) }}"
                                class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="p-4 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($permissions as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            id="permission_{{ $permission->id }}"
                                            {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="permission_{{ $permission->id }}"
                                            class="ml-2 block text-sm text-gray-900">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.roles.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize form validation
                $("#editRoleForm").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3,
                            maxlength: 255
                        },
                        slug: {
                            required: true,
                            minlength: 3,
                            maxlength: 255,
                            pattern: /^[a-z0-9-]+$/
                        },
                        description: {
                            maxlength: 1000
                        },
                        'permissions[]': {
                            required: true,
                            minlength: 1
                        }
                    },
                    messages: {
                        name: {
                            required: "Please enter a role name",
                            minlength: "Role name must be at least 3 characters long",
                            maxlength: "Role name cannot be longer than 255 characters"
                        },
                        slug: {
                            required: "Please enter a slug",
                            minlength: "Slug must be at least 3 characters long",
                            maxlength: "Slug cannot be longer than 255 characters",
                            pattern: "Slug can only contain lowercase letters, numbers, and hyphens"
                        },
                        description: {
                            maxlength: "Description cannot be longer than 1000 characters"
                        },
                        'permissions[]': {
                            required: "Please select at least one permission",
                            minlength: "Please select at least one permission"
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        if (element.attr("type") === "checkbox") {
                            // Place error message after the checkbox grid
                            error.insertAfter(element.closest('.grid'));
                        } else {
                            error.insertAfter(element);
                        }
                        error.addClass('text-red-600 text-sm mt-1 block');
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('border-red-500').removeClass('border-gray-300');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('border-red-500').addClass('border-gray-300');
                    },
                    submitHandler: function(form) {
                        // Disable submit button to prevent double submission
                        $(form).find('button[type="submit"]').prop('disabled', true);
                        form.submit();
                    }
                });

                // Auto-generate slug from name
                $('#name').on('input', function() {
                    if (!$('#slug').data('manually-entered')) {
                        const slug = $(this)
                            .val()
                            .toLowerCase()
                            .replace(/[^a-z0-9-]/g, '-')
                            .replace(/-+/g, '-')
                            .replace(/^-|-$/g, '');
                        $('#slug').val(slug);
                    }
                });

                // Track if slug was manually entered
                $('#slug').on('input', function() {
                    $(this).data('manually-entered', true);
                });
            });
        </script>
    @endpush
@endsection
