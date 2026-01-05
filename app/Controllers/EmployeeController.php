<?php


class EmployeeController 
{
    private EmployeeService $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        AuthMiddleware::check();
        // PermissionService::hasPage('employee');

        $formelements = $this->service->getFormDependencies();

        $data = [
            'branches' => $formelements['branches'],
            'departments' => $formelements['departments'],
            'companies' => $formelements['companies'],
            'managers' => $formelements['managers'],
            'job_positions' => $formelements['job_positions'],
            'cities' => $formelements['cities'],
        ];

        view('employee.index', $data, 'app');
    }

    public function apiEmployees()
    {
        AuthMiddleware::check();

        $result = $this->service->datatable($_GET);

        // Convert raw rows into HTML using the newly created partial
        $result['rows'] = renderView('employee.partials.table-rows', [
            'rows'   => $result['rows'],
            'offset' => $result['offset']
        ]);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
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
    public function statusUpdate()
    {
        AuthMiddleware::check();

        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $this->service->statusUpdate($data['id'], $data['status']);

            echo json_encode([
                'success' => true,
                'message' => 'Employee status updated'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function view($id)
    {
        AuthMiddleware::check();
        PermissionService::hasPage('employee');

        $employee = $this->service->getById($id);

        view('employee.view', ['employee' => $employee], 'app');
    }
}
