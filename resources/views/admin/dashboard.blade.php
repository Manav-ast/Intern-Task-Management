@extends('layouts.admin') {{-- Create this layout separately below --}}

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Welcome, Admin!</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="text-lg font-semibold text-gray-700">Total Interns</h2>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $internsCount ?? 0 }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="text-lg font-semibold text-gray-700">Active Tasks</h2>
            <p class="text-2xl font-bold text-green-600 mt-2">{{ $activeTasks ?? 0 }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="text-lg font-semibold text-gray-700">Messages</h2>
            <p class="text-2xl font-bold text-purple-600 mt-2">{{ $messagesCount ?? 0 }}</p>
        </div>
    </div>
</div>
@endsection
