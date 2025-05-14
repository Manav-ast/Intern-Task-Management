@extends('layouts.guest')

@section('title', 'Intern Registration')

@section('head')
<style>
    .error {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="">
    <h2 class="text-xl font-bold mb-4">Intern Registration</h2>
    <form id="registerForm" method="POST" action="{{ route('intern.register') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Name</label>
            <input type="text" name="name" class="w-full mt-1 p-2 border rounded" value="{{ old('name') }}" >
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" class="w-full mt-1 p-2 border rounded" value="{{ old('email') }}" >
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" name="password" class="w-full mt-1 p-2 border rounded" >
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full mt-1 p-2 border rounded" >
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">Register</button>

        <div class="text-center mt-4 text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('intern.login') }}" class="text-indigo-600 hover:underline font-medium">Login here</a>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        © {{ now()->year }} Intern Task Manager
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#registerForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    equalTo: "[name='password']"
                }
            },
            messages: {
                name: {
                    required: "Please enter your name",
                    minlength: "Name must be at least 3 characters long"
                },
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 6 characters long"
                },
                password_confirmation: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('error');
                error.insertAfter(element);
            },
            highlight: function(element) {
                $(element).addClass('border-red-500').removeClass('border-gray-300');
            },
            unhighlight: function(element) {
                $(element).removeClass('border-red-500').addClass('border-gray-300');
            }
        });
    });
</script>
@endsection
