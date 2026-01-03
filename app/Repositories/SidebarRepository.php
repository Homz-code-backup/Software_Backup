<?php
class SidebarRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getSidebarTree(string $employeeId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.module,
                p.slug,
                p.icon,
                p.link
            FROM page_permissions pp
            JOIN pages p ON p.id = pp.page_id
            WHERE pp.employee_id = ?
            ORDER BY p.module, p.name
        ");

        $stmt->execute([$employeeId]);
        $pages = $stmt->fetchAll();

        $tree = [];
        foreach ($pages as $p) {
            $tree[$p['module']][] = $p;
        }

        return $tree;
    }
}
