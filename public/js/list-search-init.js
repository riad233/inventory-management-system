document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('[data-search-table]');

    inputs.forEach(function(input) {
        const tableId = input.getAttribute('data-search-table');
        if (tableId) {
            attachTableSearch(input.id, tableId);
        }
    });
});
