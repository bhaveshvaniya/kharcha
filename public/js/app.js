// Kharcha - Main JavaScript

// Auto-dismiss alerts after 4 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });
});

// Confirm delete for forms
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.hasAttribute('data-confirm')) {
        if (!confirm(form.getAttribute('data-confirm'))) {
            e.preventDefault();
        }
    }
});

// Number formatting helper
function formatINR(amount) {
    return '₹' + parseFloat(amount).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
