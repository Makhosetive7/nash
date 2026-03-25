<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/SalesController.php';

setCorsHeaders();
handlePreflight();

$controller = new SalesController($pdo);

if (isGet()) {
    $controller->index();
} else {
    sendResponse(false, 'Method not allowed. Use create_sale.php for POST', null, 405);
}
