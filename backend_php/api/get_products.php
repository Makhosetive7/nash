<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ProductController.php';

setCorsHeaders();
handlePreflight();

$controller = new ProductController($pdo);

if (isGet()) {
    if (isset($_GET['id'])) {
        $controller->show($_GET['id']);
    } else {
        $controller->index();
    }
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
