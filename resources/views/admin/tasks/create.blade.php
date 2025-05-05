@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto py-6">
        <h2 class="text-2xl font-semibold mb-4">Create Task</h2>

        <form action="{{ route('admin.tasks.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="title" class="block font-medium">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring">
                @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block font-medium">Description</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="due_date" class="block font-medium">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring">
                @error('due_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="status" class="block font-medium">Status</label>
                <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
                    <option value="pending" selected>Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div>
                <label for="interns" class="block font-medium">Assign to Interns</label>
                <select name="interns[]" id="interns" multiple
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring">
                    @foreach ($interns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                    @endforeach
                </select>
                @error('interns')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple interns</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create
                    Task</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#interns').select2({
                    placeholder: 'Select interns',
                    allowClear: true,
                    width: '100%'
                });
            });
        </script>
    @endpush

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush
@endsection
