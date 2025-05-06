@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Roles</h2>
            @can('create-roles')
                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
                    + Add New Role
                </a>
            @endcan
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Permissions</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($roles as $role)
                        <tr id="role-row-{{ $role->id }}">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $role->name }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                                    {{ $role->slug }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @foreach ($role->permissions as $permission)
                                    <span
                                        class="inline-block px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs mr-1 mb-1">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                @can('edit-roles')
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                        class="text-gray-600 font-medium hover:underline">Edit</a>
                                @endcan
                                @can('delete-roles')
                                    <button type="button" class="text-red-600 font-medium hover:underline"
                                        onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                                        Delete
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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

            function deleteRole(roleId, roleName) {
                Swal.fire({
                    title: 'Delete Role',
                    text: `Are you sure you want to delete "${roleName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/roles/${roleId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Remove the row with animation
                                $(`#role-row-${roleId}`).fadeOut('slow', function() {
                                    $(this).remove();
                                });

                                // Show success message
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Role deleted successfully',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                // Show error message
                                let errorMessage = 'Error deleting role. Please try again.';
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
