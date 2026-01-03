<?php


class EmployeeController
{
    private EmployeeService $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }



    public function create()
    { 
        AuthMiddleware::check();
        // PermissionService::hasPage('employee');

        $data = $this->service->getFormDependencies();

        view('employee.create', $data, 'app');
    }

    public function store()
    {
        AuthMiddleware::check();
        // PermissionService::hasAction('employee.create');

        try {
            $this->service->register($_POST);

            redirectWithFlash(
                'success',
                'Employee registered successfully',
                BASE_PATH . '/employees/create'
            );
        } catch (Exception $e) {
            redirectWithFlash(
                'error',
                $e->getMessage(),
                BASE_PATH . '/employees/create'
            );
        }
    }
}
