function attachTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (!input || !table) {
        return;
    }

    input.addEventListener('keyup', function() {
        const filter = input.value.toLowerCase();
        const tbody = table.getElementsByTagName('tbody')[0];
        if (!tbody) {
            return;
        }
        const rows = tbody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const text = rows[i].textContent || rows[i].innerText;
            rows[i].style.display = text.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
    });
}
