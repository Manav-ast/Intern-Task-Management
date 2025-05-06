<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (auth()->check())
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-type" content="{{ get_class(auth()->user()) }}">
        <meta name="user-name" content="{{ auth()->user()->name }}">
        @if (isset($otherUser))
            <meta name="other-user-id" content="{{ $otherUser->id }}">
        @endif
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-minimal@5/minimal.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .modal-backdrop {
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content {
            transition: transform 0.3s ease-in-out;
        }

        /* jQuery Validate Styles */
        .error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        input.error,
        textarea.error,
        select.error {
            border-color: #dc2626 !important;
        }

        input.valid,
        textarea.valid,
        select.valid {
            border-color: #059669 !important;
        }
    </style>

    <script>
        // Setup AJAX CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global function to show alert messages
        function showAlert(message, type = 'success') {
            const alertClass = type === 'success' ?
                'bg-green-100 border-green-500 text-green-700' :
                'bg-red-100 border-red-500 text-red-700';

            const alert = $('<div>')
                .addClass(`${alertClass} border-l-4 p-4 mb-6 rounded`)
                .attr('role', 'alert')
                .html(`<p class="font-medium">${message}</p>`)
                .hide();

            $('.container').first().prepend(alert);
            alert.fadeIn('slow');
            setTimeout(() => alert.fadeOut('slow', function() {
                $(this).remove();
            }), 3000);
        }
    </script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Utility Scripts -->
    <script src="{{ asset('js/utils.js') }}"></script>
    @stack('scripts')
</body>

</html>
