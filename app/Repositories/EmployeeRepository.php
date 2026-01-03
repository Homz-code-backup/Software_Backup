<?php

class EmployeeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT 
                e.id,
                u.status,
                e.employee_id,
                e.full_name,
                e.official_email,
                e.official_number,
                e.ctc,
                e.ctc_in_words,
                e.date_of_joining,
                e.job_position,
                d.department_name,
                b.branch_name,
                c.name AS company_name,
                u.role
            FROM employee e
            LEFT JOIN department d ON e.department_id = d.id
            LEFT JOIN branch b ON e.branch_id = b.id
            LEFT JOIN company c ON e.company_id = c.id
            LEFT JOIN users u ON e.employee_id = u.employee_id           
            WHERE u.status IN ('Active', 'Registered') ORDER BY e.employee_id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeById(string $employeeId): ?Employee
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM employee
            WHERE employee_id = ?
            LIMIT 1
        ");
        $stmt->execute([$employeeId]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    private function mapToModel(array $row): Employee
    {
        return new Employee($row);
    }

    public function getBranches(): array
    {
        return $this->db
            ->query("SELECT id, branch_name FROM branch")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBranchesByCity($cityId): array
    {
        $stmt = $this->db->prepare("SELECT id, branch_name FROM branch WHERE city_id = ?");
        $stmt->execute([$cityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartments(): array
    {
        return $this->db
            ->query("SELECT id, department_name FROM department")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompanies(): array
    {
        return $this->db
            ->query("SELECT id, name FROM company")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getManagers(): array
    {
        return $this->db
            ->query("
                SELECT employee_id, full_name 
                FROM employee 
                WHERE job_position IN ('Manager','HR','MD')
            ")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEmployee(array $d): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO employee 
            (employee_id, full_name, official_email, official_number,
             date_of_joining, job_position, branch_id, department_id, company_id, employee_image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $d['employee_id'],
            $d['full_name'],
            $d['official_email'],
            $d['official_number'],
            $d['date_of_joining'],
            $d['job_position'],
            $d['branch_id'],
            $d['department_id'],
            $d['company_id'],
            'hzi.png'
        ]);

        // Create user login
        $userRepo = new UserRepository();

        $userRepo->createUser([
            'employee_id'   => $d['employee_id'],
            'email'         => $d['official_email'],
            'password_hash' => password_hash('homz@2025', PASSWORD_DEFAULT),
            'role'          => 'Employee',
            'token'         => bin2hex(random_bytes(16)),
            'status'        => 'Active'
        ]);
    }

    public function getCityIdByEmployee($employeeId) {
        $stmt = $this->db->prepare("SELECT b.city_id FROM employee e 
                                     JOIN branch b ON e.branch_id = b.id 
                                     WHERE e.employee_id = ?");
        $stmt->execute([$employeeId]);
        return $stmt->fetchColumn();
    }

    public function getCityName($cityId) {
        $stmt = $this->db->prepare("SELECT name FROM cities WHERE id = ?");
        $stmt->execute([$cityId]);
        return $stmt->fetchColumn();
    }

    public function findByEmployeeId(string $employeeId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM employee WHERE employee_id = ?");
        $stmt->execute([$employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
