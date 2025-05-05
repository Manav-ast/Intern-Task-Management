@extends('layouts.guest')

@section('title', 'Admin Registration')

@section('content')
    <h2 class="text-xl font-bold text-center mb-6">Admin Registration</h2>
    <form method="POST" action="{{ route('admin.register') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Name</label>
            <input type="text" name="name" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" name="password" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Register</button>
    </form>
@endsection
