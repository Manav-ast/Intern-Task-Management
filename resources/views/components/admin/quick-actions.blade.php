@props(['actions' => []])

<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Quick Actions</h2>
    </div>
    <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
        @can('create-tasks')
            <a href="{{ route('admin.tasks.create') }}"
                class="w-full inline-flex items-center justify-center px-4 py-2 sm:py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Task
            </a>
        @endcan
        @can('create-interns')
            <a href="{{ route('admin.interns.create') }}"
                class="w-full inline-flex items-center justify-center px-4 py-2 sm:py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Add New Intern
            </a>
        @endcan
    </div>
</div>
