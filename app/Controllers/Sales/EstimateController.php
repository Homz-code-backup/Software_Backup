<?php
class EstimateController
{
    private EstimateService $service;
 
    public function __construct(EstimateService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        AuthMiddleware::check();
        PermissionService::hasPage('estimate');

        $data = $this->service->getFormDependencies();

        view('sales.index', $data, 'app');
    }

    public function apiEstimates()
    {
        AuthMiddleware::check();

        $result = $this->service->datatable($_GET);

        // Convert raw rows into HTML using a partial (we'll create this next)
        $result['rows'] = renderView('sales.partials.table-rows', [
            'rows'   => $result['rows'],
            'offset' => $result['offset']
        ]);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
