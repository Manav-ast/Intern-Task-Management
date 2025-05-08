// Import Select2 from npm package
import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.min.css';

// Select2 global initialization
export function initializeSelect2() {
    // Initialize Select2 Elements with search and multiple selection
    $('.select2-multiple').each(function () {
        const $this = $(this);

        $(this).select2({
            theme: 'default', // Using default theme for better styling with Tailwind
            placeholder: $this.data('placeholder') || 'Select options',
            allowClear: true,
            width: '100%',
            dropdownCssClass: 'select2-dropdown-default',
            selectionCssClass: 'select2-selection-default',
            closeOnSelect: false,
        });
    });
}

// Initialize on document ready
$(document).ready(function () {
    initializeSelect2();
});

// Add custom Select2 styling
const style = document.createElement('style');
style.textContent = `
    .select2-container--default .select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        cursor: text;
        padding: 0.5rem;
        min-height: 42px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px #6366f1;
        outline: 0;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eef2ff;
        border: 1px solid #c7d2fe;
        border-radius: 0.25rem;
        margin-right: 5px;
        margin-top: 3px;
        padding: 3px 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #6366f1;
        margin-right: 5px;
        font-weight: bold;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #4f46e5;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #6366f1;
        outline: none;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6366f1;
        color: white;
    }

    .select2-container--default .select2-selection--multiple.error {
        border-color: #dc2626 !important;
    }
`;

document.head.appendChild(style);
