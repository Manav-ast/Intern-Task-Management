@extends('layouts.intern') {{-- Create this layout separately below --}}

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Hello, {{ auth('intern')->user()->name }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="text-lg font-semibold text-gray-700">Your Assigned Tasks</h2>
            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $taskCount ?? 0 }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5">
            <h2 class="text-lg font-semibold text-gray-700">Messages from Admin</h2>
            <p class="text-2xl font-bold text-indigo-600 mt-2">{{ $messageCount ?? 0 }}</p>
        </div>
    </div>
</div>
@endsection
