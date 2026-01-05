<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = Database::getConnection();

echo "TABLES:\n";
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

echo "COLUMNS for project_customers:\n";
$stmt = $db->query("DESCRIBE project_customers");
foreach ($stmt->fetchAll() as $col) {
    echo "- {$col['Field']} ({$col['Type']})\n";
}
