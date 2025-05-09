@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Admins</h2>
            @can('create-admins')
                <a href="{{ route('admin.admins.create') }}"
                    class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
                    + Add New Admin
                </a>
            @endcan
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Roles</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($admins as $admin)
                        <tr id="admin-row-{{ $admin->id }}">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $admin->name }}</td>
                            <td class="px-6 py-4">{{ $admin->email }}</td>
                            <td class="px-6 py-4">
                                @foreach ($admin->roles as $role)
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                @if (!$admin->isSuperAdmin())
                                    @can('edit-admins')
                                        @if ($admin->id !== Auth::guard('admin')->id())
                                            <a href="{{ route('admin.admins.edit', $admin) }}"
                                                class="text-gray-600 font-medium hover:underline">Edit</a>
                                        @endif
                                    @endcan
                                    @if (!$admin->hasRole('admin') || Auth::guard('admin')->user()->isSuperAdmin())
                                        @can('delete-admins')
                                            @if ($admin->id !== Auth::guard('admin')->id())
                                                <button type="button" class="text-red-600 font-medium hover:underline"
                                                    onclick="deleteAdmin({{ $admin->id }}, '{{ $admin->name }}')">
                                                    Delete
                                                </button>
                                            @endif
                                        @endcan
                                    @endif
                                @endif
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
                @if (session('swal-success'))
                    Swal.fire({
                        title: 'Success!',
                        text: '{{ session('swal-success') }}',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                @endif

                // Show error message if exists
                @if (session('swal-error'))
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ session('swal-error') }}',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                @endif
            });

            function deleteAdmin(adminId, adminName) {
                Swal.fire({
                    title: 'Delete Admin',
                    text: `Are you sure you want to delete "${adminName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/admins/${adminId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) {
                                // Remove the row with animation
                                $(`#admin-row-${adminId}`).fadeOut('slow', function() {
                                    $(this).remove();
                                });

                                // Show success message
                                Swal.fire({
                                    title: response.title || 'Success!',
                                    text: response.message || 'Admin deleted successfully',
                                    icon: response.type || 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Check if there are no more admins and reload the page
                                    if ($('tr[id^="admin-row-"]').length === 0) {
                                        window.location.reload();
                                    }
                                });
                            },
                            error: function(xhr) {
                                // Show error message
                                let errorMessage = 'Error deleting admin. Please try again.';
                                let errorTitle = 'Error!';
                                let errorType = 'error';

                                if (xhr.responseJSON) {
                                    errorMessage = xhr.responseJSON.error || errorMessage;
                                    errorTitle = xhr.responseJSON.title || errorTitle;
                                    errorType = xhr.responseJSON.type || errorType;
                                }

                                Swal.fire({
                                    title: errorTitle,
                                    text: errorMessage,
                                    icon: errorType,
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
