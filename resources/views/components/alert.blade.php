@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const success = @json(session('success'));
            Swal.fire({
                title: success.title || 'Success!',
                text: success.message,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4F46E5', // Indigo-600 to match your theme
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
@endif
