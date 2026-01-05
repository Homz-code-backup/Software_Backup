<?php
class EmployeeService
{
    private EmployeeRepository $repo;

    public function __construct()
    {
        $this->repo = new EmployeeRepository();
    }

    public function getAll($status): array
    {
        return $this->repo->getAll($status);
    }
    public function getById(string $id): ?Employee
    {
        return $this->repo->getEmployeeById($id); 
    }

    public function datatable(array $params)
    {
        return $this->repo->datatable($params);
    }

    public function getFormDependencies(): array
    {
        return [
            'branches'     => $this->repo->getBranches(),
            'departments'  => $this->repo->getDepartments(),
            'companies'    => $this->repo->getCompanies(),
            'managers'     => $this->repo->getManagers(),
            'job_positions'    => $this->repo->getJobPositions(),
            'cities'       => $this->repo->getCities(),
        ];
    }

    public function register(array $data): void
    {
        if (empty($data['employee_id']) || empty($data['official_email'])) {
            throw new Exception('Required fields missing');
        }

        $this->repo->createEmployee($data);
    }
    public function statusUpdate($id, $status)
    {
        $this->repo->statusUpdate($id, $status);
    }
}
