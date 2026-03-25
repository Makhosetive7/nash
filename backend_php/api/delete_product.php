<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ProductController.php';

setCorsHeaders();
handlePreflight();

$controller = new ProductController($pdo);

if (isDelete()) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $controller->destroy($id);
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
