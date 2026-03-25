<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/SalesController.php';

setCorsHeaders();
handlePreflight();

$controller = new SalesController($pdo);

if (isPost()) {
    $data = getJsonInput();
    $controller->store($data);
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
