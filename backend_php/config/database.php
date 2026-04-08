<?php
// Use MYSQL_URL from Railway (contains full connection string)
$mysqlUrl = getenv('MYSQL_URL');

if ($mysqlUrl) {
    // Parse the connection URL: mysql://user:pass@host:port/database
    $parsed = parse_url($mysqlUrl);
    define('DB_HOST', $parsed['host']);
    define('DB_NAME', ltrim($parsed['path'], '/'));
    define('DB_USER', $parsed['user']);
    define('DB_PASS', $parsed['pass']);
    define('DB_PORT', $parsed['port'] ?? '3306');
} else {
    // Fallback to local development
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'nashmart_db');
    define('DB_USER', 'nashmart_user');
    define('DB_PASS', 'nashmart123');
    define('DB_PORT', '3306');
}

define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $pdo = new PDO(
        $dsn,
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
