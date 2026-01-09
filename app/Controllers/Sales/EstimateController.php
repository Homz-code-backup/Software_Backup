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
        $data['title'] = 'ERP-Projects';
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

    public function saveProject()
    {
        AuthMiddleware::check();
        $res = $this->service->saveProject($_POST);
        header('Content-Type: application/json');

        if (is_array($res)) {
            echo json_encode(['success' => true, 'id' => $res['id'], 'token' => $res['token']]);
        } else {
            echo json_encode(['success' => (bool)$res]);
        }
        exit;
    }

    public function saveEstimate()
    {
        AuthMiddleware::check();
        $res = $this->service->saveEstimate($_POST);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$res, 'id' => $res]);
        exit;
    }

    public function apiGetProject()
    {
        AuthMiddleware::check();
        $token = $_GET['token'] ?? $_GET['id'] ?? '';
        $project = $this->service->getProjectByToken($token);
        header('Content-Type: application/json');
        echo json_encode($project);
        exit;
    }

    public function viewProject($token)
    {
        AuthMiddleware::check();

        $project = $this->service->getProjectByToken($token);
        if (!$project) {
            http_response_code(404);
            echo "Project not found";
            exit;
        }

        // Auto-generate timeline if missing
        $this->service->generateProjectTimeline($project['id']);

        $estimates = $this->service->getProjectEstimates($project['id']);
        $dashboard = $this->service->getProjectDashboardData($project['id']);

        // Override timeline with advanced data
        $timeline = $this->service->getAdvancedTimeline($project['id']);

        view('sales.project-view', [
            'project'   => $project,
            'estimates' => $estimates,
            'timeline'  => $timeline,
            'documents' => $dashboard['documents'],
            'receipts'  => $dashboard['receipts'], // Kept just in case, though tab removed
            'title'     => 'Project Dashboard - ' . $project['name']
        ], 'app');
    }

    public function updateTimelineDate()
    {
        AuthMiddleware::check();
        $id = $_POST['id'] ?? null;
        $date = $_POST['date'] ?? null;

        if ($id && $date) {
            $res = $this->service->rescheduleTimelineStep($id, $date);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$res]);
            exit;
        }

        http_response_code(400);
        exit;
    }

    public function saveTimeline()
    {
        // ... kept for backward compatibility or remove? 
        // User asked for specific new timeline system.
        AuthMiddleware::check();
        // ... code
    }

    public function saveReceipt()
    {
        AuthMiddleware::check();
        $res = $this->service->saveReceipt($_POST);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$res]);
        exit;
    }

    public function saveDocument()
    {
        AuthMiddleware::check();

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../../public/uploads/projects/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . '_' . basename($_FILES['file']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $data = $_POST;
                $data['file_path'] = 'public/uploads/projects/' . $fileName;
                $res = $this->service->saveDocument($data);

                header('Content-Type: application/json');
                echo json_encode(['success' => (bool)$res]);
                exit;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Upload failed']);
        exit;
    }
    public function deleteDocument()
    {
        AuthMiddleware::check();
        $id = $_POST['id'] ?? null;
        if ($id) {
            $res = $this->service->deleteDocument($id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$res]);
            exit;
        }
        http_response_code(400);
        exit;
    }
}
