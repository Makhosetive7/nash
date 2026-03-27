<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/SalesController.php';

setCorsHeaders();
handlePreflight();

$controller = new SalesController($pdo);

if (isDelete()) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $controller->destroy($id);
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
