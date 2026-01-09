<?php

class EmployeeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(array $status): array
    {
        // Create placeholders like ?, ?
        $placeholders = implode(',', array_fill(0, count($status), '?'));

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
            d.id as department_id,
            b.branch_name,
            b.id as branch_id,
            c.name AS company_name,
            u.role
        FROM employee e
        LEFT JOIN department d ON e.department_id = d.id
        LEFT JOIN branch b ON e.branch_id = b.id
        LEFT JOIN company c ON e.company_id = c.id
        LEFT JOIN users u ON e.employee_id = u.employee_id           
        WHERE u.status IN ($placeholders)
        ORDER BY e.employee_id DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($status); // safely bind array values

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function datatable(array $p)
    {
        $page    = max(1, (int)($p['page'] ?? 1));
        $perPage = max(1, (int)($p['limit'] ?? 10));
        $offset  = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (!empty($p['search'])) {
            $where[] = "(e.full_name LIKE ? OR e.official_email LIKE ?)";
            $params[] = "%{$p['search']}%";
            $params[] = "%{$p['search']}%";
        }

        if (!empty($p['department_id'])) {
            $where[] = "e.department_id = ?";
            $params[] = $p['department_id'];
        }

        if (!empty($p['branch_id'])) {
            $where[] = "e.branch_id = ?";
            $params[] = $p['branch_id'];
        }

        if (!empty($p['job_position'])) {
            $where[] = "e.job_position = ?";
            $params[] = $p['job_position'];
        }

        if (!empty($p['city_id'])) {
            $where[] = "b.city_id = ?";
            $params[] = $p['city_id'];
        }

        if (!empty($p['status'])) {
            $status = is_array($p['status']) ? $p['status'] : explode(',', $p['status']);
            $placeholders = implode(',', array_fill(0, count($status), '?'));
            $where[] = "u.status IN ($placeholders)";
            foreach ($status as $s) {
                $params[] = $s;
            }
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT SQL_CALC_FOUND_ROWS
                e.*, d.department_name, b.branch_name, c.name as city_name, u.status as user_status
            FROM employee e
            LEFT JOIN department d ON d.id = e.department_id
            LEFT JOIN branch b ON b.id = e.branch_id
            LEFT JOIN cities c ON c.id = b.city_id 
            LEFT JOIN users u ON u.employee_id = e.employee_id
            $whereSql
            ORDER BY e.id DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $this->db->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            'rows' => $rows,
            'offset' => $offset,
            'pagination' => [
                'current' => $page,
                'total' => ceil($total / $perPage),
                'total_records' => $total
            ]
        ];
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
            ->query("SELECT id, branch_name, city_id FROM branch")
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

    public function getCities(): array
    {
        return $this->db
            ->query("SELECT id, name FROM cities ORDER BY name ASC")
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

    public function getJobPositions(): array
    {
        return $this->db
            ->query("
                SELECT DISTINCT job_position
                FROM employee
                ORDER BY job_position ASC;
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

    public function getCityIdByEmployee($employeeId)
    {
        $stmt = $this->db->prepare("SELECT b.city_id FROM employee e 
                                     JOIN branch b ON e.branch_id = b.id 
                                     WHERE e.employee_id = ?");
        $stmt->execute([$employeeId]);
        return $stmt->fetchColumn();
    }

    public function getCityName($cityId)
    {
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
    function statusUpdate($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE employee_id = ?");
        $stmt->execute([
            $status,
            $id
        ]);
    }

    public function getActiveEmployees(): array
    {
        return $this->db->query("
            SELECT e.employee_id, e.full_name, e.job_position 
            FROM employee e 
            JOIN users u ON e.employee_id = u.employee_id 
            WHERE u.status = 'Active' 
            ORDER BY e.full_name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
