<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../controllers/SalesController.php';

setCorsHeaders();
handlePreflight();

$controller = new SalesController($pdo);

if (isPut()) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $data = getJsonInput();

    if (!$data) {
        parse_str(file_get_contents("php://input"), $data);
    }

    $controller->update($id, $data);
} else {
    sendResponse(false, 'Method not allowed', null, 405);
}
