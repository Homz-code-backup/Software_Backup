<?php

class PermissionAdminRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getActiveEmployees(): array
    {
        return $this->pdo
            ->query("SELECT users.id as user_id, users.employee_id, employee.full_name FROM employee 
            LEFT JOIN users ON employee.employee_id = users.employee_id            
            WHERE users.status='Active'")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPages(): array
    {
        return $this->pdo
            ->query("SELECT * FROM pages ORDER BY module, name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubPermissionsByPage(): array
    {
        $rows = $this->pdo->query("
            SELECT * FROM sub_permissions
            ORDER BY permission_name
        ")->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[$r['page_id']][] = $r;
        }
        return $out;
    }

    public function getUserPageAssignments(): array
    {
        $rows = $this->pdo->query("
            SELECT employee_id, page_id
            FROM page_permissions
        ")->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[$r['employee_id']][] = $r['page_id'];
        }
        return $out;
    }

    public function getUserSubAssignments(): array
    {
        $rows = $this->pdo->query("
            SELECT employee_id, sub_permission_id
            FROM sub_permission_assignments
        ")->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[$r['employee_id']][] = $r['sub_permission_id'];
        }
        return $out;
    }

    public function getCities(): array
    {
        return $this->pdo->query("SELECT id, name FROM cities ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBranches(): array
    {
        return $this->pdo->query("SELECT id, branch_name as name FROM branch ORDER BY branch_name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserCitiesAssignments(): array
    {
        $rows = $this->pdo->query("SELECT user_id, city_id, sub_permission_id FROM user_cities")
            ->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[$r['user_id']][] = $r['city_id'];
        }
        return $out;
    }

    public function getUserBranchesAssignments(): array
    {
        $rows = $this->pdo->query("SELECT user_id, branch_id, sub_permission_id FROM user_branches")
            ->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[$r['user_id']][] = $r['branch_id'];
        }
        return $out;
    }

    public function updatePermissions(string $empId, array $pages, array $subs, array $cities = [], array $branches = []): void
    {
        $this->pdo->prepare("DELETE FROM page_permissions WHERE employee_id=?")
            ->execute([$empId]);

        $this->pdo->prepare("DELETE FROM sub_permission_assignments WHERE employee_id=?")
            ->execute([$empId]);

        // Get user_id for user_cities and user_branches
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE employee_id = ?");
        $stmt->execute([$empId]);
        $userId = $stmt->fetchColumn();

        if ($userId) {
            $this->pdo->prepare("DELETE FROM user_cities WHERE user_id=? AND sub_permission_id=14")->execute([$userId]);
            $this->pdo->prepare("DELETE FROM user_branches WHERE user_id=? AND sub_permission_id=14")->execute([$userId]);

            foreach ($cities as $cId) {
                $this->pdo->prepare("INSERT INTO user_cities (user_id, city_id, sub_permission_id) VALUES (?,?,?)")
                    ->execute([$userId, $cId, 14]);
            }

            foreach ($branches as $bId) {
                $this->pdo->prepare("INSERT INTO user_branches (user_id, branch_id, sub_permission_id) VALUES (?,?,?)")
                    ->execute([$userId, $bId, 14]);
            }
        }

        foreach ($pages as $p) {
            $this->pdo->prepare(
                "INSERT INTO page_permissions (employee_id,page_id) VALUES (?,?)"
            )->execute([$empId, $p]);
        }

        foreach ($subs as $s) {
            $this->pdo->prepare(
                "INSERT INTO sub_permission_assignments (employee_id,sub_permission_id) VALUES (?,?)"
            )->execute([$empId, $s]);
        }
    }
    public function addPage(array $d): void
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO pages (name,module,slug,icon,link)
        VALUES (?,?,?,?,?)
    ");
        $stmt->execute([
            $d['name'],
            $d['module'],
            $d['slug'],
            $d['icon'],
            $d['link']
        ]);
    }

    public function updatePage(array $d): void
    {
        $stmt = $this->pdo->prepare("
        UPDATE pages SET name=?, module=?, slug=?, icon=?, link=?
        WHERE id=?
    ");
        $stmt->execute([
            $d['name'],
            $d['module'],
            $d['slug'],
            $d['icon'],
            $d['link'],
            $d['id']
        ]);
    }

    public function deletePage(int $id): void
    {
        $this->pdo->prepare("DELETE FROM pages WHERE id=?")->execute([$id]);
    }

    public function addSubPermission(array $d): void
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO sub_permissions (page_id, permission_name, slug)
        VALUES (?,?,?)
    ");
        $stmt->execute([$d['page_id'], $d['permission_name'], $d['slug']]);
    }

    public function updateSubPermission(array $d): void
    {
        $stmt = $this->pdo->prepare("
        UPDATE sub_permissions SET permission_name=?, slug=?
        WHERE id=?
    ");
        $stmt->execute([$d['permission_name'], $d['slug'], $d['id']]);
    }

    public function deleteSubPermission(int $id): void
    {
        $this->pdo->prepare("DELETE FROM sub_permissions WHERE id=?")->execute([$id]);
    }
}
