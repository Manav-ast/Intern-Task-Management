<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guest Page')</title>

    <!-- Add your CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Main Content -->
    <div class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-md p-8 bg-white shadow-md rounded-lg">
            @yield('content')
        </div>
    </div>

    <!-- Optional: Add your footer or additional scripts here -->
    @yield('scripts')
</body>
</html>
