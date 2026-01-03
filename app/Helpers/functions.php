<?php
function app_path(string $path = ''): string
{
    return dirname(__DIR__) . '/' . ltrim($path, '/');
}


if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

if (!function_exists('redirect')) {
    function redirect($path)
    {
        header("Location: " . BASE_PATH . $path);
        exit;
    }
}


if (!function_exists('redirectWithFlash')) {
    function redirectWithFlash($key, $message, $path)
    {
        $_SESSION[$key] = $message;
        header("Location: " . $path);
        exit;
    }
}

if (!function_exists('view')) {
    function view($path, $data = [], $layout = null)
    {
        // Convert 'auth.login' to 'auth/login'
        $viewPath = str_replace('.', '/', $path);
        
        // 1. Prepare data
        if (!empty($data)) {
            extract($data);
        }

        // 2. Resolve the view file path
        $fullPath = __DIR__ . '/../../views/' . $viewPath . '.php';

        if (!file_exists($fullPath)) {
            die("View file not found: $viewPath");
        }

        // 3. Handle Layouts
        if ($layout) {
            // If a layout is specified, we render the layout.
            // The layout file is responsible for including the $viewPath.
            // We pass $view (the absolute path to the content view) to the layout.
            
            $layoutPath = __DIR__ . '/../../views/layouts/' . $layout . '.php';
            
            if (!file_exists($layoutPath)) {
                die("Layout file not found: $layout");
            }

            // The layout file will include $view
            $view = $fullPath; 
            require $layoutPath;

        } else {
            // No layout, just render the view directly
            require $fullPath;
        }
    }
}

if (!function_exists('indian_number_format')) {
    function indian_number_format($number)
    {
        $number = round($number);
        $num = (string) $number;
        $lastThree = substr($num, -3);
        $restUnits = substr($num, 0, -3);

        if ($restUnits != '') {
            $restUnits = preg_replace("/\B(?=(?:\d{2})+(?!\d))/", ",", $restUnits);
            $formatted = $restUnits . ',' . $lastThree;
        } else {
            $formatted = $lastThree;
        }
        return $formatted;
    }
}
