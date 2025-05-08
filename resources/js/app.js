import './bootstrap';
import './chat';
import './select2-setup';

// Add CSRF token to all fetch requests
document.addEventListener('DOMContentLoaded', function () {
    // Get the CSRF token from the meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Add a global fetch error handler
    window.handleFetchError = async (response) => {
        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || 'Network response was not ok');
        }
        return response;
    };

    // Add a global fetch success handler
    window.handleFetchSuccess = (data) => {
        return data;
    };

    // Global fetch headers
    window.fetchHeaders = {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };
});
