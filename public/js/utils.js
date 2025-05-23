// Reusable delete confirmation function using SweetAlert2
function confirmDelete(title, text, callback) {
    Swal.fire({
        title: title || 'Are you sure?',
        text: text || "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626', // red-600
        cancelButtonColor: '#6b7280', // gray-500
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// Handle success messages with SweetAlert2
document.addEventListener('DOMContentLoaded', function () {
    const success = @json(session('success'));
    if (success) {
        Swal.fire({
            title: success.title || 'Success!',
            text: success.message,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4F46E5', // Indigo-600 to match your theme
            timer: 3000,
            timerProgressBar: true
        });
    }
});
