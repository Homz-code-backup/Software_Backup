<?php
class EmployeeService
{
    private EmployeeRepository $repo;

    public function __construct()
    {
        $this->repo = new EmployeeRepository();
    }

    public function getAll(): array
    {
        return $this->repo->getAll(); 
    }
    
    public function getFormDependencies(): array
    {
        return [
            'branches'     => $this->repo->getBranches(),
            'departments'  => $this->repo->getDepartments(),
            'companies'    => $this->repo->getCompanies(),
            'managers'     => $this->repo->getManagers(),
        ];
    }

    public function register(array $data): void
    {
        if (empty($data['employee_id']) || empty($data['official_email'])) {
            throw new Exception('Required fields missing');
        }

        $this->repo->createEmployee($data);
    }
}
