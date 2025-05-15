@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Manage Interns</h1>
            @can('create-interns')
                <a href="{{ route('admin.interns.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New Intern
                </a>
            @endcan
        </div>

        @if (session('success'))
            <div id="success-alert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded"
                role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($interns as $intern)
                            <tr id="intern-row-{{ $intern->id }}"
                                class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 intern-name">{{ $intern->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $intern->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                    @can('edit-interns')
                                        <a href="{{ route('admin.interns.edit', $intern) }}"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-150 ease-in-out">
                                            <span class="hidden sm:inline">Edit</span>
                                            <svg class="w-5 h-5 inline-block sm:hidden" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete-interns')
                                        <button type="button"
                                            onclick="confirmDelete('Delete Intern', `Are you sure you want to delete ${$('#intern-row-' + {{ $intern->id }}).find('.intern-name').text()}? This action cannot be undone.`, () => deleteIntern({{ $intern->id }}))"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-150 ease-in-out">
                                            <span class="hidden sm:inline">Delete</span>
                                            <svg class="w-5 h-5 inline-block sm:hidden" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Auto-hide success message
                if ($('#success-alert').length > 0) {
                    setTimeout(function() {
                        $('#success-alert').fadeOut('slow');
                    }, 3000);
                }

                // Set CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });

            // Reusable delete confirmation function
            function confirmDelete(title, text, callback) {
                Swal.fire({
                    title: title || 'Are you sure?',
                    text: text || "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            }

            function deleteIntern(internId) {
                $.ajax({
                    url: `/admin/interns/${internId}`,
                    type: 'DELETE',
                    success: function(response) {
                        // Remove the row with animation
                        $(`#intern-row-${internId}`).fadeOut('slow', function() {
                            $(this).remove();
                        });

                        // Show success message using SweetAlert2
                        Swal.fire({
                            title: 'Success!',
                            text: 'Intern deleted successfully',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        // Show error message using SweetAlert2
                        let errorMessage = 'Error deleting intern. Please try again.';
                        let errorTitle = 'Error!';
                        let showFooter = false;
                        let footerText = '';

                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;

                            // Set a more specific title for task assignment errors
                            if (errorMessage.includes('assigned tasks')) {
                                errorTitle = 'Tasks Assigned';
                                showFooter = true;
                                footerText =
                                    'To unassign tasks, go to each task and remove this intern from the assignees.';
                            } else if (xhr.status === 403) {
                                errorTitle = 'Permission Denied';
                            }
                        }

                        Swal.fire({
                            title: errorTitle,
                            text: errorMessage,
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            footer: showFooter ? footerText : null
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
