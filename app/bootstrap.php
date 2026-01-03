<?php
require_once __DIR__ . '/Helpers/SessionHelper.php';
require_once __DIR__ . '/Helpers/functions.php';

spl_autoload_register(function ($class) {
   

    // 1. Handle Namespaced Classes (PSR-4 style relative to app/)
    $relativeClass = str_replace('\\', '/', $class);
    $networkPath = __DIR__ . '/' . $relativeClass . '.php';
    if (file_exists($networkPath)) {
        require_once $networkPath;
        return;
    }

    // 2. Handle Legacy Flat Classes (search in specific folders)
    $paths = [
        __DIR__ . '/Controllers/',
        __DIR__ . '/Services/',
        __DIR__ . '/Repositories/',
        __DIR__ . '/Middleware/',
        __DIR__ . '/Models/',
        __DIR__ . '/Helpers/',
        __DIR__ . '/Core/',
    ];

    foreach ($paths as $path) {
        // Only try this if it's not a namespaced class (no backslash) OR if the legacy structure relies on it
        // The original code passed the full $class (which might contain backslashes) to the path.
        // For standard "Class" it works.
        // For "Namespace\Class", the original code created "Path/Namespace\Class.php" which usually fails.
        // We keep this loop fallback for non-namespaced classes.
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
