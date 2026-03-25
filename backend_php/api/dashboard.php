<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

setCorsHeaders();
handlePreflight();

$controller = new DashboardController($pdo);

if (isGet()) {
    $controller->index();
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
