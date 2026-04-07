<?php
// Railway environment variables or fallback to local development
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'nashmart_db');
define('DB_USER', getenv('MYSQLUSER') ?: 'nashmart_user');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'nashmart123');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}