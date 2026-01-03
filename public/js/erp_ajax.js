function erpAjax({ url, data = {}, method = 'POST', button = null, confirmText = null, onSuccess = () => {}, onError = () => {} }) {
    if (confirmText && !confirm(confirmText)) return;
    button && (button.disabled = true);

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => res.success ? onSuccess(res) : onError(res.message))
    .catch(() => onError('Network error'))
    .finally(() => button && (button.disabled = false));
}
