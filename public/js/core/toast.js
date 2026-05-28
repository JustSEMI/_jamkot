document.addEventListener('DOMContentLoaded', function() {
    const toastModern = document.getElementById('toast-modern');
    
    if (toastModern) {
        setTimeout(() => {
            tutupToastModern();
        }, 3000); 
    }
});

function tutupToastModern() {
    const toastModern = document.getElementById('toast-modern');
    if (toastModern) {
        toastModern.classList.add('ngilang');
        setTimeout(() => toastModern.remove(), 400);
    }
}