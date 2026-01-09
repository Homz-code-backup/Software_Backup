<?php

class PermissionAdminRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Get all pages
    public function getAllPages(): array
    {
        $stmt = $this->db->query("SELECT * FROM pages ORDER BY module, name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get sub-permissions grouped by page_id
    public function getSubPermissionsByPage(): array
    {
        $stmt = $this->db->query("
            SELECT 
                sp.*,
                sp.requires_city_scope as has_city_constraint,
                sp.requires_branch_scope as has_branch_constraint
            FROM sub_permissions sp
            ORDER BY sp.page_id, sp.permission_name
        ");
        $subs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($subs as $sub) {
            $grouped[$sub['page_id']][] = $sub;
        }

        return $grouped;
    }

    // Get active employees
    public function getActiveEmployees(): array
    {
        $stmt = $this->db->query("SELECT * FROM employee
        LEFT JOIN users ON employee.employee_id = users.employee_id
         WHERE users.status = 'active' ORDER BY full_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user page assignments
    public function getUserPageAssignments(): array
    {
        $stmt = $this->db->query("SELECT employee_id, page_id FROM page_permissions");
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($assignments as $a) {
            $grouped[$a['employee_id']][] = $a['page_id'];
        }

        return $grouped;
    }

    // Get user sub-permission assignments
    public function getUserSubAssignments(): array
    {
        $stmt = $this->db->query("SELECT employee_id, sub_permission_id FROM sub_permission_assignments");
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($assignments as $a) {
            $grouped[$a['employee_id']][] = $a['sub_permission_id'];
        }

        return $grouped;
    }

    // Get cities
    public function getCities(): array
    {
        $stmt = $this->db->query("SELECT id, name FROM cities ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get branches
    public function getBranches(): array
    {
        $stmt = $this->db->query("SELECT id, branch_name FROM branch ORDER BY branch_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user city assignments
    public function getUserCitiesAssignments(): array
    {
        $stmt = $this->db->query("SELECT user_id, city_id, sub_permission_id FROM user_cities");
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($assignments as $a) {
            $grouped[$a['user_id']][$a['sub_permission_id']][] = $a['city_id'];
        }

        return $grouped;
    }

    // Get user branch assignments
    public function getUserBranchesAssignments(): array
    {
        $stmt = $this->db->query("SELECT user_id, branch_id, sub_permission_id FROM user_branches");
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($assignments as $a) {
            $grouped[$a['user_id']][$a['sub_permission_id']][] = $a['branch_id'];
        }

        return $grouped;
    }

    // Helper: Get users.id from employee_id
    private function getUserIdFromEmployeeId($employeeId): ?int
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE employee_id = ?");
        $stmt->execute([$employeeId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : null;
    }

    // Update permissions for a user
    public function updatePermissions($employeeId, array $pageIds, array $subPermissionIds, array $cities, array $branches): void
    {
        // Get the numeric user ID for foreign key constraints
        $userId = $this->getUserIdFromEmployeeId($employeeId);

        if (!$userId) {
            throw new Exception("User not found for employee_id: {$employeeId}");
        }

        // Fetch which sub-permissions require scoping among the assigned ones
        $placeholders = implode(',', array_fill(0, count($subPermissionIds) ?: 0, '?'));
        $scopedSubs = [];
        if (!empty($subPermissionIds)) {
            $stmt = $this->db->prepare("SELECT id, requires_city_scope, requires_branch_scope FROM sub_permissions WHERE id IN ($placeholders)");
            $stmt->execute($subPermissionIds);
            $scopedSubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Delete existing assignments
        $this->db->prepare("DELETE FROM page_permissions WHERE employee_id = ?")->execute([$employeeId]);
        $this->db->prepare("DELETE FROM sub_permission_assignments WHERE employee_id = ?")->execute([$employeeId]);
        $this->db->prepare("DELETE FROM user_cities WHERE user_id = ?")->execute([$userId]);
        $this->db->prepare("DELETE FROM user_branches WHERE user_id = ?")->execute([$userId]);

        // Insert new page assignments (uses employee_id)
        $stmt = $this->db->prepare("INSERT INTO page_permissions (employee_id, page_id) VALUES (?, ?)");
        foreach ($pageIds as $pageId) {
            $stmt->execute([$employeeId, $pageId]);
        }

        // Insert new sub-permission assignments (uses employee_id)
        $stmt = $this->db->prepare("INSERT INTO sub_permission_assignments (employee_id, sub_permission_id) VALUES (?, ?)");
        foreach ($subPermissionIds as $subId) {
            $stmt->execute([$employeeId, $subId]);
        }

        // Insert city assignments per scoped sub-permission
        $cityStmt = $this->db->prepare("INSERT INTO user_cities (user_id, city_id, sub_permission_id) VALUES (?, ?, ?)");
        foreach ($scopedSubs as $sub) {
            if ($sub['requires_city_scope']) {
                foreach ($cities as $cityId) {
                    $cityStmt->execute([$userId, $cityId, $sub['id']]);
                }
            }
        }

        // Insert branch assignments per scoped sub-permission
        $branchStmt = $this->db->prepare("INSERT INTO user_branches (user_id, branch_id, sub_permission_id) VALUES (?, ?, ?)");
        foreach ($scopedSubs as $sub) {
            if ($sub['requires_branch_scope']) {
                foreach ($branches as $branchId) {
                    $branchStmt->execute([$userId, $branchId, $sub['id']]);
                }
            }
        }
    }

    // Add a new page
    public function addPage(array $data): void
    {
        $stmt = $this->db->prepare("INSERT INTO pages (page_name, page_url, module, icon) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['page_name'],
            $data['page_url'],
            $data['module'],
            $data['icon'] ?? null
        ]);
    }

    // Update a page
    public function updatePage(array $data): void
    {
        $stmt = $this->db->prepare("UPDATE pages SET page_name = ?, page_url = ?, module = ?, icon = ? WHERE id = ?");
        $stmt->execute([
            $data['page_name'],
            $data['page_url'],
            $data['module'],
            $data['icon'] ?? null,
            $data['id']
        ]);
    }

    // Delete a page
    public function deletePage(int $id): void
    {
        $this->db->prepare("DELETE FROM pages WHERE id = ?")->execute([$id]);
    }

    // Add a new sub-permission
    public function addSubPermission(array $data): void
    {
        $stmt = $this->db->prepare("INSERT INTO sub_permissions (page_id, permission_name, slug, requires_city_scope, requires_branch_scope) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['page_id'],
            $data['permission_name'],
            $data['slug'],
            isset($data['requires_city_scope']) ? 1 : 0,
            isset($data['requires_branch_scope']) ? 1 : 0
        ]);
    }

    // Update a sub-permission
    public function updateSubPermission(array $data): void
    {
        $stmt = $this->db->prepare("UPDATE sub_permissions SET page_id = ?, permission_name = ?, slug = ?, requires_city_scope = ?, requires_branch_scope = ? WHERE id = ?");
        $stmt->execute([
            $data['page_id'],
            $data['permission_name'],
            $data['slug'],
            isset($data['requires_city_scope']) ? 1 : 0,
            isset($data['requires_branch_scope']) ? 1 : 0,
            $data['id']
        ]);
    }

    // Delete a sub-permission
    public function deleteSubPermission(int $id): void
    {
        $this->db->prepare("DELETE FROM sub_permissions WHERE id = ?")->execute([$id]);
    }
}
