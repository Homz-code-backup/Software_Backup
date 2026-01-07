<?php
class EstimateRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function datatable(array $p)
    {
        $page    = max(1, (int)($p['page'] ?? 1));
        $perPage = max(1, (int)($p['limit'] ?? 10));
        $offset  = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        if (!empty($p['search'])) {
            $where[] = "(pc.name LIKE ? OR pc.email LIKE ? OR pc.project_name LIKE ? OR pc.project_reference LIKE ? )";
            $params[] = "%{$p['search']}%";
            $params[] = "%{$p['search']}%"; 
            $params[] = "%{$p['search']}%";
            $params[] = "%{$p['search']}%";
        }
 
        if (!empty($p['city_id'])) {
            $where[] = "pc.city = ?";
            $params[] = $p['city_id'];
        }

        if (!empty($p['branch_id'])) {
            $where[] = "pc.branch_id = ?";
            $params[] = $p['branch_id']; 
        }

        if(!empty($p['user_branches'])) {
            $where[] = "pc.branch_id IN (" . implode(',', array_map(fn($b) => (int)$b['branch_id'], $p['user_branches'])) . ")";
        }

        if(!empty($p['user_cities'])) {
            $where[] = "pc.city IN (" . implode(',', array_map(fn($c) => (int)$c['city_id'], $p['user_cities'])) . ")";
        }

        if (!empty($p['active_status'])) {
            $status = is_array($p['active_status']) ? $p['active_status'] : explode(',', $p['active_status']);
            $placeholders = implode(',', array_fill(0, count($status), '?'));
            $where[] = "pc.status IN ($placeholders)";
            foreach ($status as $s) {
                $params[] = $s;
            }
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT SQL_CALC_FOUND_ROWS
                pc.*, b.branch_name, c.name as city_name
            FROM project_customers pc
            LEFT JOIN branch b ON b.id = pc.branch_id
            LEFT JOIN cities c ON c.id = pc.city
            $whereSql
            ORDER BY pc.id DESC
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

    public function getBranches(): array
    {
        return $this->db
            ->query("SELECT id, branch_name, city_id FROM branch")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCities(): array
    {
        return $this->db
            ->query("SELECT id, name FROM cities ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllCustomers()
    {
        $stmt = $this->db->prepare("SELECT * FROM project_customers  ORDER BY id DESC;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getUserBranches($sub_id=null){
        $stmt = $this->db->prepare("SELECT * FROM user_branches WHERE user_id = ? AND sub_permission_id = ?");
        $stmt->execute([Auth::user()->id, $sub_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function getUserCities($sub_id=null){
        $stmt = $this->db->prepare("SELECT * FROM user_cities WHERE user_id = ? AND sub_permission_id = ?");
        $stmt->execute([Auth::user()->id, $sub_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
