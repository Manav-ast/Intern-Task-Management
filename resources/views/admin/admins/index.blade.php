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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
                        <tr>
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
                                @can('edit-admins')
                                    <a href="{{ route('admin.admins.edit', $admin) }}"
                                        class="text-gray-600 font-medium hover:underline">Edit</a>
                                @endcan
                                @can('delete-admins')
                                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline"
                                        id="delete-admin-form-{{ $admin->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 font-medium hover:underline"
                                            onclick="confirmDelete('Delete Admin', 'Are you sure you want to delete this admin? This action cannot be undone.', () => document.getElementById('delete-admin-form-{{ $admin->id }}').submit())">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
