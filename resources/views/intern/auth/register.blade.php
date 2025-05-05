@extends('layouts.guest')

@section('title', 'Intern Registration')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded-xl p-6">
    <h2 class="text-xl font-bold mb-4">Intern Registration</h2>
    <form method="POST" action="{{ route('intern.register') }}">
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
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Register</button>
    </form>
</div>
@endsection
