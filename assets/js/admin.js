document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-confirm]');
    if (!btn) {
        return;
    }
    if (!confirm(btn.getAttribute('data-confirm'))) {
        e.preventDefault();
    }
});
