document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded');

    // Подтверждение опасных действий
    const dangerButtons = document.querySelectorAll('.btn-danger:not([disabled])');
    dangerButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Вы уверены? Это действие нельзя отменить.')) {
                e.preventDefault();
            }
        });
    });
});