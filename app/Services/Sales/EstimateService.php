<?php

class EstimateService
{
    private EstimateRepository $repo;
    private EmployeeRepository $empRepo;

    public function __construct(EstimateRepository $repo, EmployeeRepository $empRepo)
    {
        $this->repo = $repo;
        $this->empRepo = $empRepo;
    }

    public function datatable(array $params)
    {
        // Inject user-specific filters for the "Load" permission (ID 14)
        $params['user_branches'] = $this->repo->getUserBranches(14);
        $params['user_cities'] = $this->repo->getUserCities(14);
        $params['user_employee_id'] = Auth::user()->employee_id;

        return $this->repo->datatable($params);
    }

    public function getFormDependencies(): array
    {
        return [
            'branches' => $this->repo->getBranches(),
            'cities'   => $this->repo->getCities(),
            'user_branches' => $this->repo->getUserBranches(14),
            'user_cities' => $this->repo->getUserCities(14),
            'user_employee_id' => Auth::user()->employee_id,
            'employees' => $this->empRepo->getActiveEmployees(),
        ];
    }

    public function getClients()
    {
        return $this->repo->getAllCustomers();
    }

    public function getProjectByToken($token)
    {
        return $this->repo->getProjectByToken($token);
    }

    public function getEstimateByToken($token)
    {
        return $this->repo->getEstimateByToken($token);
    }

    public function saveProject(array $data)
    {
        return $this->repo->saveProject($data);
    }

    public function getProjectEstimates($projectId)
    {
        return $this->repo->getProjectEstimates($projectId);
    }

    public function saveEstimate(array $data)
    {
        return $this->repo->saveEstimate($data);
    }

    public function getProjectDashboardData($projectId)
    {
        return [
            'timeline' => $this->repo->getTimeline($projectId),
            'documents' => $this->repo->getDocuments($projectId),
            'receipts' => $this->repo->getReceipts($projectId)
        ];
    }

    public function saveTimeline($data)
    {
        return $this->repo->saveTimeline($data);
    }
    public function saveReceipt($data)
    {
        return $this->repo->saveReceipt($data);
    }
    public function saveDocument($data)
    {
        return $this->repo->saveDocument($data);
    }

    public function deleteDocument($id)
    {
        $doc = $this->repo->getDocumentById($id);
        if ($doc && file_exists(__DIR__ . '/../../../' . $doc['file_path'])) {
            unlink(__DIR__ . '/../../../' . $doc['file_path']);
        }
        return $this->repo->deleteDocument($id);
    }

    public function generateProjectTimeline($projectId)
    {
        // 1. Check if already generated
        $existing = $this->repo->getTrackingTimeline($projectId);
        if (!empty($existing)) {
            return $existing;
        }

        // 2. Fetch Project Estimates to calculate budget
        $estimates = $this->repo->getProjectEstimates($projectId);
        $totalBudget = 0;
        foreach ($estimates as $e) {
            if ($e['status'] == 'active' || $e['status'] == 'booked') {
                $totalBudget += $e['project_cost'];
            }
        }

        // 3. Generate from today (or ideally booking date, defaulting to today)
        return $this->generateTimelineFromBooking($projectId, date('Y-m-d'), $totalBudget);
    }

    public function generateTimelineFromBooking($projectId, $bookingDate, $budget)
    {
        $steps = $this->repo->getMasterSteps();
        $startDate = new DateTime($bookingDate);
        $currentDate = clone $startDate;

        $this->repo->clearProjectTimeline($projectId); // Clear old simple timeline? No, this is different table.

        foreach ($steps as $step) {
            // Check condition
            if ($step['is_conditional']) {
                if ($step['conditional_type'] === 'budget') {
                    if ($budget < (float)$step['condition_value']) {
                        continue; // Skip if budget is lower than threshold (e.g. skip if < 15L)
                    }
                }
            }

            // Calculate Date
            // Gap days adds to previous step's date? Or cumulative?
            // "Gap days from previous step to plan this one"
            // So: Next Date = Current Date + Gap Days.

            $gap = (int)$step['gap_days'];
            if ($gap > 0) {
                // Add gap days, skipping Sundays maybe? User didn't specify. logic: simple add.
                $currentDate->modify("+$gap days");
            }

            $this->repo->createTrackingEntry($projectId, $step['id'], $currentDate->format('Y-m-d'));
        }

        return true;
    }

    public function rescheduleTimelineStep($trackingId, $newDate)
    {
        return $this->repo->updateTrackingDate($trackingId, $newDate);
    }

    public function getAdvancedTimeline($projectId)
    {
        return $this->repo->getTrackingTimeline($projectId);
    }
}
