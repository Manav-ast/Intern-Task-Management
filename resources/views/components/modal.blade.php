@props(['id'])

<div id="{{ $id }}" class="fixed inset-0 hidden z-50">
    <!-- Modal backdrop -->
    <div class="modal-backdrop fixed inset-0 bg-gray-500 bg-opacity-75"></div>

    <!-- Modal content -->
    <div class="modal-content fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl transform transition-all max-w-lg w-full">
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
