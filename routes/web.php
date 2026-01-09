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

    // Check for exact match
    if ($reqMethod === $method && $uri === $path) {
        dispatch($handler);
    }

    // Check for regex match if path contains regex characters
    if ($reqMethod === $method && strpos($path, '(') !== false) {
        $pattern = "#^" . $path . "$#";
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            dispatch($handler, $matches);
        }
    }
}

function dispatch($handler, $params = [])
{
    [$controllerPath, $action] = explode('@', $handler);
    $parts = preg_split('/[\\\\\/]/', $controllerPath);
    $controllerClassName = end($parts);
    require_once __DIR__ . "/../app/Controllers/$controllerPath.php";
    $container = new Container();
    $instance = $container->resolve($controllerClassName);
    call_user_func_array([$instance, $action], $params);
    exit;
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
route('GET', '/projects', 'Sales/EstimateController@index');
route('GET', '/api/estimates', 'Sales/EstimateController@apiEstimates');
route('POST', '/api/project/save', 'Sales/EstimateController@saveProject');
route('POST', '/api/estimate/save', 'Sales/EstimateController@saveEstimate');
route('POST', '/api/project/timeline/save', 'Sales/EstimateController@saveTimeline');
route('POST', '/api/project/receipt/save', 'Sales/EstimateController@saveReceipt');
route('POST', '/api/project/document/save', 'Sales/EstimateController@saveDocument');

route('GET', '/api/project/get', 'Sales/EstimateController@apiGetProject');
route('GET', '/projects/view/([a-f0-9]+)', 'Sales/EstimateController@viewProject');
route('POST', '/estimates/update-timeline-date', 'Sales/EstimateController@updateTimelineDate');
route('POST', '/projects/documents/upload', 'Sales/EstimateController@saveDocument');
route('POST', '/projects/documents/delete', 'Sales/EstimateController@deleteDocument');


route('GET',  '/superadmin/assign-permissions', 'SuperAdminPermissionController@index');
route('POST', '/superadmin/assign-permissions', 'SuperAdminPermissionController@store');

route('GET',  '/superadmin/manage-permissions', 'SuperAdminManagePermissionController@index');
route('POST', '/superadmin/manage-permissions/page', 'SuperAdminManagePermissionController@pageAction');
route('POST', '/superadmin/manage-permissions/sub',  'SuperAdminManagePermissionController@subAction');


http_response_code(404);
echo "404 - Page Not Found";
