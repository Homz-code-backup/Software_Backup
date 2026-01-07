<?php

function route($method, $path, $handler)
{
    $reqMethod = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Remove base path
    if (strpos($uri, BASE_PATH) === 0) {
        $uri = substr($uri, strlen(BASE_PATH));
    }

    if ($uri === '') $uri = '/';
    $uri = rtrim($uri, '/') ?: '/';

    if ($reqMethod === $method && $uri === $path) {
        [$controllerPath, $action] = explode('@', $handler);

        // Split by / or \ to get the class name
        $parts = preg_split('/[\\\\\/]/', $controllerPath);
        $controllerClassName = end($parts);

        // Require the specific file
        require_once __DIR__ . "/../app/Controllers/$controllerPath.php";

        // Resolve using the short class name
        $container = new Container();
        $instance = $container->resolve($controllerClassName); 

        $instance->$action();
        exit;
    }
}

/* ROUTES */
route('GET',  '/',         'AuthController@loginForm');
route('POST', '/login',    'AuthController@login');
route('GET',  '/logout',   'AuthController@logout');
route('GET',  '/dashboard', 'DashboardController@index');
route('GET', '/notifications/read', 'NotificationController@read');
route('GET',  '/forgot-password', 'AuthController@forgotForm');
route('POST', '/forgot-password', 'AuthController@sendResetLink');

route('GET',  '/reset-password', 'AuthController@resetForm');
route('POST', '/reset-password', 'AuthController@resetPassword');
route('GET',  '/employees/create', 'EmployeeController@create');
route('POST', '/employees/store',  'EmployeeController@store');
route('POST', '/employees/statusupdate', 'EmployeeController@statusUpdate');
route('GET',  '/employees', 'EmployeeController@index');
route('GET', '/employees/view/{id}', 'EmployeeController@view');
route('GET', '/api/employees', 'EmployeeController@apiEmployees');
route('GET', '/sales', 'Sales/EstimateController@index');
route('GET', '/api/estimates', 'Sales/EstimateController@apiEstimates');


route('GET',  '/superadmin/assign-permissions', 'SuperAdminPermissionController@index');
route('POST', '/superadmin/assign-permissions', 'SuperAdminPermissionController@store');

route('GET',  '/superadmin/manage-permissions', 'SuperAdminManagePermissionController@index');
route('POST', '/superadmin/manage-permissions/page', 'SuperAdminManagePermissionController@pageAction');
route('POST', '/superadmin/manage-permissions/sub',  'SuperAdminManagePermissionController@subAction');


http_response_code(404);
echo "404 - Page Not Found";
