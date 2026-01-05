<?php

class SuperAdminPermissionService
{
    private PermissionAdminRepository $repo;
    private EmployeeRepository $employeeRepo;

    public function __construct()
    {
        $this->repo = new PermissionAdminRepository();
        $this->employeeRepo = new EmployeeRepository();
    }

    public function getEmployees(): array
    {
        return $this->repo->getActiveEmployees();
    }

    public function getPagesGrouped(): array
    {
        $pages = $this->repo->getAllPages();
        $grouped = [];

        foreach ($pages as $p) {
            $grouped[$p['module']][] = $p;
        }

        return $grouped;
    }

    public function getSubPermissions(): array
    {
        return $this->repo->getSubPermissionsByPage();
    }

    public function getUserPageAssignments(): array
    {
        return $this->repo->getUserPageAssignments();
    }

    public function getUserSubAssignments(): array
    {
        return $this->repo->getUserSubAssignments();
    }

    public function getCities(): array
    {
        return $this->repo->getCities();
    }

    public function getBranches(): array
    {
        return $this->repo->getBranches();
    }

    public function getUserCitiesAssignments(): array
    {
        return $this->repo->getUserCitiesAssignments();
    }

    public function getUserBranchesAssignments(): array
    {
        return $this->repo->getUserBranchesAssignments();
    }

    public function update(array $data): void
    {
        $this->repo->updatePermissions(
            $data['employee_id'],
            $data['assigned_pages'] ?? [],
            $data['assigned_sub_permissions'] ?? [],
            $data['selected_cities'] ?? [],
            $data['selected_branches'] ?? []
        );

        $employee = $this->employeeRepo->getEmployeeById($data['employee_id']);

        $employeeName = $employee ? $employee->full_name : $data['employee_id'];

        (new NotificationService())->notifyUser(
            $_SESSION['user_id'],
            'System Notification',
            'Permissions have been assigned to' . $employeeName,
            '/dashboard',
            'success'
        );
    }
}
