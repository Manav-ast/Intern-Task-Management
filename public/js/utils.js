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
