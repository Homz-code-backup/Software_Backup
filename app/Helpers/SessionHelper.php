<?php

/**
 * --------------------------------------------------
 * SESSION HELPER – ERP v2
 * --------------------------------------------------
 * Handles:
 * - Flash messages
 * - Safe redirects
 * - Bootstrap alerts
 * --------------------------------------------------
 */

/**
 * Ensure session is started only once
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set a flash message
 *
 * @param string $type success | error | warning | info
 * @param string $message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Display flash messages (Bootstrap styled)
 */
function displayFlash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }

    $typeMap = [
        'success' => 'alert-success',
        'error'   => 'alert-danger',
        'warning' => 'alert-warning',
        'info'    => 'alert-info',
    ];

    foreach ($_SESSION['flash'] as $type => $message) {
        $class = $typeMap[$type] ?? 'alert-secondary';
        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        echo <<<HTML
<div class="alert {$class} alert-dismissible fade show mt-2" role="alert">
    {$safeMessage}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
HTML;
    }

    // Auto dismiss after 5 seconds
    echo <<<JS
<script>
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }
        });
    }, 5000);
});
</script>
JS;

    // Clear flash messages
    unset($_SESSION['flash']);
}

/**
 * Redirect with flash message
 *
 * @param string $type
 * @param string $message
 * @param string $url
 */
function redirectWithFlash(string $type, string $message, string $url): void
{
    setFlash($type, $message);
    header("Location: {$url}");
    exit;
}
