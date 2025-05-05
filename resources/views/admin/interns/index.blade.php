@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Manage Interns</h1>
            <a href="{{ route('admin.interns.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Intern
            </a>
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
                                    <a href="{{ route('admin.interns.edit', $intern) }}"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150 ease-in-out">
                                        <span class="hidden sm:inline">Edit</span>
                                        <svg class="w-5 h-5 inline-block sm:hidden" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button" onclick="showDeleteModal({{ $intern->id }})"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150 ease-in-out">
                                        <span class="hidden sm:inline">Delete</span>
                                        <svg class="w-5 h-5 inline-block sm:hidden" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal id="delete-modal">
        <div class="sm:flex sm:items-start">
            <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Intern</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Are you sure you want to delete <span
                            id="intern-name-placeholder"></span>? This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
            <button type="button" id="confirm-delete"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-150 ease-in-out">
                Delete
            </button>
            <button type="button" onclick="closeModal('delete-modal')"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors duration-150 ease-in-out">
                Cancel
            </button>
        </div>
    </x-modal>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let internIdToDelete = null;

                // Set up CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Auto-hide success message
                if ($('#success-alert').length > 0) {
                    setTimeout(function() {
                        $('#success-alert').fadeOut('slow');
                    }, 3000);
                }

                // Delete confirmation
                $('#confirm-delete').click(function() {
                    if (!internIdToDelete) return;

                    $.ajax({
                        url: `/admin/interns/${internIdToDelete}`,
                        type: 'DELETE',
                        success: function(response) {
                            // Remove the row with animation
                            $(`#intern-row-${internIdToDelete}`).fadeOut('slow', function() {
                                $(this).remove();
                            });

                            // Show success message
                            showAlert('Intern deleted successfully');

                            // Close modal
                            closeModal('delete-modal');
                        },
                        error: function(xhr) {
                            // Show error message
                            let errorMessage = 'Error deleting intern. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }
                            showAlert(errorMessage, 'error');
                            closeModal('delete-modal');
                        }
                    });
                });

                // Close modal when clicking outside
                $(document).on('click', '.modal-backdrop', function() {
                    closeModal('delete-modal');
                });
            });

            function showDeleteModal(internId) {
                internIdToDelete = internId;
                // Get the intern name from the row
                const internName = $(`#intern-row-${internId}`).find('.intern-name').text();
                $('#intern-name-placeholder').text(internName);
                openModal('delete-modal');
            }

            function openModal(modalId) {
                $(`#${modalId}`).removeClass('hidden').addClass('block');
                $('body').addClass('overflow-hidden');
            }

            function closeModal(modalId) {
                $(`#${modalId}`).removeClass('block').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        </script>
    @endpush
@endsection
