<?php

// -------------------------------
// ENVIRONMENT DETECTION
// -------------------------------
if (!defined('ENVIRONMENT')) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define(
        'ENVIRONMENT',
        ($host === 'localhost' || $host === '127.0.0.1')
            ? 'local'
            : 'live'
    );
}

// -------------------------------
// DATABASE CREDENTIALS
// -------------------------------
switch (ENVIRONMENT) {

    case 'local':
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'erp_v2'); // change if needed
        define('DB_USER', 'root');
        define('DB_PASS', '');
        break;

    case 'live':
        define('DB_HOST', 'localhost'); // update if different
        define('DB_NAME', 'erp_v2');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        break;

    default:
        die('Unknown environment');
}

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }


        return self::$instance;
    }

    public static function connect(): PDO
    {
        return self::getConnection();
    }
}
