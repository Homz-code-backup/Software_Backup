<?php
require_once __DIR__ . '/app/Core/Database.php';
$db = Database::getConnection();

echo "TABLE: project_customers\n";
$stmt = $db->query("DESCRIBE project_customers");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo $col['Field'] . "\n";
}
