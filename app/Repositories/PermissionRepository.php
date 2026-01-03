<?php
class PermissionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Get page permissions (pages user can access)
     */
    public function getPagePermissions(string $employeeId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.slug
            FROM page_permissions pp
            JOIN pages p ON p.id = pp.page_id
            WHERE pp.employee_id = ?
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
 
    /**
     * Get action permissions (sub permissions)
     */
    public function getSubPermissions(string $employeeId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT sp.slug
            FROM sub_permission_assignments spa
            JOIN sub_permissions sp ON sp.id = spa.sub_permission_id
            WHERE spa.employee_id = ?
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
