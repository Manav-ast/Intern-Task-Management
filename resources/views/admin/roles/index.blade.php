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
                        <tr>
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
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 font-medium hover:underline"
                                            onclick="return confirm('Are you sure?')">Delete</button>
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
