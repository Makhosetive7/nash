<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/ProductController.php';

setCorsHeaders();
handlePreflight();

$controller = new ProductController($pdo);

if (isPost()) {
    $controller->store($_POST, $_FILES);
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
?>