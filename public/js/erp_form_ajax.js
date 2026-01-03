document.addEventListener('submit', e => {
    if (!e.target.matches('form[data-ajax]')) return;
    e.preventDefault();

    const form = e.target;
    erpAjax({
        url: form.action,
        data: Object.fromEntries(new FormData(form)),
        button: form.querySelector('button[type="submit"]')
    });
});
