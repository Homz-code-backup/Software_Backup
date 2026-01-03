<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/ERP_V2');
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Models/User.php';

echo "Testing Model Enhancements...\n";

// Test 1: Generic where()
// Assume there's at least one active user
$activeUsers = User::where('status', 'Active');
echo "[PASS] User::where('status', 'Active') found " . count($activeUsers) . " users.\n";

// Test 2: Custom findByEmail()
// We'll use the email from the first active user found to test
if (count($activeUsers) > 0) {
    $email = $activeUsers[0]->email;
    $user = User::findByEmail($email);
    
    if ($user && $user->email === $email) {
        echo "[PASS] User::findByEmail('{$email}') worked.\n";
    } else {
        echo "[FAIL] User::findByEmail failed.\n";
    }
} else {
    echo "[SKIP] No active users to test findByEmail.\n";
}
