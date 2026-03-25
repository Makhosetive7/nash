<?php

function sendResponse($success, $message, $data = null, $statusCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);

    $response = [
        'success' => $success,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response);
    exit;
}

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateRequired($fields, $data)
{
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

function setCorsHeaders()
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

function handlePreflight()
{
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function getJsonInput()
{
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

function isPost()
{
    return getRequestMethod() === 'POST';
}

function isGet()
{
    return getRequestMethod() === 'GET';
}

function isPut()
{
    return getRequestMethod() === 'PUT';
}

function isDelete()
{
    return getRequestMethod() === 'DELETE';
}
