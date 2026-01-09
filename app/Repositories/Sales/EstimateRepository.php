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
            $where[] = "(p.name LIKE ? OR p.email LIKE ? OR p.project_name LIKE ? OR p.project_reference LIKE ? )";
            $params[] = "%{$p['search']}%";
            $params[] = "%{$p['search']}%";
            $params[] = "%{$p['search']}%";
            $params[] = "%{$p['search']}%";
        }

        if (!empty($p['tab'])) {
            $tab = $p['tab'];
            // Map 'registered' tab to 'active' status in DB
            if ($tab === 'registered') {
                $where[] = "p.status = 'active'";
            } elseif ($tab === 'booked') {
                $where[] = "p.status = 'booked'";
            } elseif ($tab === 'hold') {
                $where[] = "p.status = 'hold'";
            }
        }

        if (!empty($p['city_id'])) {
            $where[] = "p.city = ?";
            $params[] = $p['city_id'];
        }

        if (!empty($p['branch_id'])) {
            $where[] = "p.branch_id = ?";
            $params[] = $p['branch_id'];
        }

        if (!empty($p['user_branches'])) {
            $where[] = "p.branch_id IN (" . implode(',', array_map(fn($b) => (int)$b['branch_id'], $p['user_branches'])) . ")";
        } elseif (!empty($p['user_cities'])) {
            $where[] = "p.city IN (" . implode(',', array_map(fn($c) => (int)$c['city_id'], $p['user_cities'])) . ")";
        } else {
            $where[] = "p.relationship_manager = ? OR p.designer = ? OR p.qc_team_member_id = ?";
            $params[] = $p['user_employee_id'];
            $params[] = $p['user_employee_id'];
            $params[] = $p['user_employee_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT SQL_CALC_FOUND_ROWS
                p.*, p.id as id, b.branch_name, c.name as city_name, COUNT(e.id) as estimate_count
            FROM projects p
            LEFT JOIN estimates e ON p.id = e.project_id
            LEFT JOIN branch b ON b.id = p.branch_id
            LEFT JOIN cities c ON c.id = p.city
            $whereSql
            GROUP BY p.id
            ORDER BY p.id DESC
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
        $stmt = $this->db->prepare("
            SELECT e.*, p.*, e.id as id FROM estimates e
            JOIN projects p ON p.id = e.project_id
            ORDER BY e.id DESC;
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getUserBranches($sub_id = null)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_branches WHERE user_id = ? AND sub_permission_id = ?");
        $stmt->execute([Auth::user()->id, $sub_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function getUserCities($sub_id = null)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_cities WHERE user_id = ? AND sub_permission_id = ?");
        $stmt->execute([Auth::user()->id, $sub_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectByToken($token)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   c.name as city_name, 
                   b.branch_name,
                   rm.full_name as rm_name,
                   des.full_name as designer_name,
                   qc.full_name as qc_name
            FROM projects p
            LEFT JOIN cities c ON p.city = c.id
            LEFT JOIN branch b ON p.branch_id = b.id
            LEFT JOIN employee rm ON p.relationship_manager = rm.employee_id
            LEFT JOIN employee des ON p.designer = des.employee_id
            LEFT JOIN employee qc ON p.qc_team_member_id = qc.employee_id
            WHERE p.token = ?
        ");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEstimateByToken($token)
    {
        $stmt = $this->db->prepare("SELECT * FROM estimates WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProjectEstimates($projectId)
    {
        $stmt = $this->db->prepare("SELECT * FROM estimates WHERE project_id = ? ORDER BY id DESC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveProject(array $data)
    {
        $id = $data['id'] ?? null;
        unset($data['id']);

        if ($id) {
            $fields = [];
            foreach (array_keys($data) as $key) {
                $fields[] = "$key = ?";
            }
            $sql = "UPDATE projects SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(array_merge(array_values($data), [$id]));
        } else {
            $data['token'] = bin2hex(random_bytes(16));
            $data['status'] = $data['status'] ?? 'active'; // Default status
            $keys = array_keys($data);
            $placeholders = array_fill(0, count($keys), '?');
            $sql = "INSERT INTO projects (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute(array_values($data))) {
                $newId = $this->db->lastInsertId();
                // Update with generated reference
                // Format: MMMYYYY-ID (e.g. DEC2025-2492)
                $ref = strtoupper(date('M')) . date('Y') . '-' . $newId;
                $this->db->prepare("UPDATE projects SET project_reference = ? WHERE id = ?")->execute([$ref, $newId]);

                return ['id' => $newId, 'token' => $data['token']];
            }
            return false;
        }
    }

    public function saveEstimate(array $data)
    {
        $id = $data['id'] ?? null;
        unset($data['id']);

        if ($id) {
            $fields = [];
            foreach (array_keys($data) as $key) {
                $fields[] = "$key = ?";
            }
            $sql = "UPDATE estimates SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(array_merge(array_values($data), [$id]));
        } else {
            $data['token'] = bin2hex(random_bytes(16));
            $data['status'] = $data['status'] ?? 'draft'; // Default status

            $keys = array_keys($data);
            $placeholders = array_fill(0, count($keys), '?');
            $sql = "INSERT INTO estimates (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute(array_values($data))) {
                $newId = $this->db->lastInsertId();
                // Update with generated reference
                $ref = strtoupper(date('M')) . date('Y') . '-' . $newId;
                $this->db->prepare("UPDATE estimates SET quote_reference = ? WHERE id = ?")->execute([$ref, $newId]);
                return $newId;
            }
            return false;
        }
    }

    public function getTimeline($projectId)
    {
        $stmt = $this->db->prepare("SELECT * FROM project_timeline WHERE project_id = ? ORDER BY event_date DESC, created_at DESC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocuments($projectId)
    {
        $stmt = $this->db->prepare("SELECT * FROM project_documents WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReceipts($projectId)
    {
        $stmt = $this->db->prepare("SELECT * FROM project_receipts WHERE project_id = ? ORDER BY payment_date DESC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveTimeline($data)
    {
        $stmt = $this->db->prepare("INSERT INTO project_timeline (project_id, title, description, event_date, created_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['project_id'],
            $data['title'],
            $data['description'],
            $data['event_date'],
            Auth::user()->id
        ]);
    }

    public function saveReceipt($data)
    {
        $stmt = $this->db->prepare("INSERT INTO project_receipts (project_id, amount, payment_date, payment_mode, transaction_ref, remarks) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['project_id'],
            $data['amount'],
            $data['payment_date'],
            $data['payment_mode'],
            $data['transaction_ref'],
            $data['remarks']
        ]);
    }

    public function saveDocument($data)
    {
        $stmt = $this->db->prepare("INSERT INTO project_documents (project_id, title, file_path, uploaded_by) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['project_id'],
            $data['title'],
            $data['file_path'],
        ]);
    }

    public function getDocumentById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM project_documents WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteDocument($id)
    {
        $stmt = $this->db->prepare("DELETE FROM project_documents WHERE id = ?");
        return $stmt->execute([$id]);
    }


    // --- Advanced Timeline Methods ---

    public function getMasterSteps()
    {
        return $this->db->query("SELECT * FROM timeline_master_steps ORDER BY stage_number ASC, step_order ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrackingTimeline($projectId)
    {
        // Fetch tracking data joined with master steps
        $stmt = $this->db->prepare("
            SELECT t.*, m.stage_number, m.stage_label, m.step_name, m.step_order, m.is_conditional
            FROM project_timeline_tracking t
            JOIN timeline_master_steps m ON t.step_id = m.id
            WHERE t.project_id = ?
            ORDER BY m.stage_number ASC, m.step_order ASC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clearProjectTimeline($projectId)
    {
        $stmt = $this->db->prepare("DELETE FROM project_timeline_tracking WHERE project_id = ?");
        return $stmt->execute([$projectId]);
    }

    public function createTrackingEntry($projectId, $stepId, $plannedDate)
    {
        $stmt = $this->db->prepare("INSERT INTO project_timeline_tracking (project_id, step_id, planned_date, status) VALUES (?, ?, ?, 'pending')");
        return $stmt->execute([$projectId, $stepId, $plannedDate]);
    }

    public function updateTrackingDate($id, $date)
    {
        $stmt = $this->db->prepare("UPDATE project_timeline_tracking SET rescheduled_date = ? WHERE id = ?");
        return $stmt->execute([$date, $id]);
    }
}
