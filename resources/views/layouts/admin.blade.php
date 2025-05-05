<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-blue-800 text-white p-6 space-y-4">
            <h2 class="text-xl font-bold mb-4">Admin Panel</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block hover:text-gray-300">Dashboard</a>
                {{-- <a href="{{ route('admin.interns.index') }}" class="block hover:text-gray-300">Manage Interns</a>
                <a href="{{ route('admin.tasks.index') }}" class="block hover:text-gray-300">Manage Tasks</a>
                <a href="{{ route('admin.messages.index') }}" class="block hover:text-gray-300">Messages</a> --}}
                <form action="{{ route('admin.logout') }}" method="POST">@csrf
                    <button class="mt-4 text-sm text-red-200 hover:text-red-400">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 bg-gray-50">
            @yield('content')
        </main>
    </div>
</body>
</html>
