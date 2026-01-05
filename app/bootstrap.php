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
        // 2a. Check direct file
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // 2b. Check in subfolders (e.g., Services/Sales/EstimateService.php)
        // This allows organizing files into modules like "Sales", "HR", etc.
        if (is_dir($path)) {
            $subfolders = glob($path . '*', GLOB_ONLYDIR);
            foreach ($subfolders as $subfolder) {
                $file = $subfolder . '/' . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    }
});
