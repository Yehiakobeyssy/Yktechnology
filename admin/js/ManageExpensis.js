document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-link')) {
        e.preventDefault();
        const deleteConfirmation = confirm('Are you sure you want to delete this expense?');
        if (deleteConfirmation) {
            const expenseID = e.target.getAttribute('data-id');
            window.location.href = `ManageExpensis.php?do=delete&id=${expenseID}`;
        }
    }
});