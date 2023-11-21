<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST,PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: *");
    header("HTTP/1.1 200 OK");
    exit();
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header("Content-Type: application/json");


$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestURI = $_SERVER['REQUEST_URI'];

// API endpoints and their corresponding actions
$endpoints = [
    '/api/login' => [
        'POST' => 'login_user',
    ],
    '/api/resource' => [
        'GET' => 'get_resource',
        'POST' => 'create_user',
    ],
    '/api/campaign/{id}' => [
        'GET' => 'get_campaign_by_id',
        'PUT' => 'update_campaign',
        'DELETE' => 'delete_campaign'
    ],
    '/api/domainlog' => [
        'POST' => 'create_log'
    ],
    '/api/domainlog/{id}' => [
        'GET' => 'domainlog'
    ],
    '/api/campaign' => [
        'POST' => 'create_campaign',
    ],
    '/api/user-campaign/{id}' => [
        'GET' => 'campaigns_by_client_id'
    ],
    '/api/users' => [
        'GET' => 'user_info',
    ],
    '/api/users/{id}' => [
        'GET' => 'user_info_by_id',
        'PUT' => 'update_user_client',
    ],
    '/api/phase/{id}' => [
        'GET' => 'phase_by_id'
    ],
    '/api/phase' => [
        'GET' => 'phase_info'
    ],

];

// Parse the request URI to determine the endpoint and method
$endpoint = null;
$action = null;

foreach ($endpoints as $pattern => $methods) {
    $pattern = str_replace('/', '\/', $pattern);
    $pattern = '/^' . str_replace('{id}', '(\d+)', $pattern) . '$/';

    if (preg_match($pattern, $requestURI, $matches) && isset($methods[$requestMethod])) {
        $endpoint = $pattern;
        $action = $methods[$requestMethod];
        break;
    }
}

if ($endpoint && $action) {
// Call the corresponding action method
    include 'api.php';

    $response = call_user_func($action, $matches);

//echo json_encode($response);
} else {
    header("HTTP/1.0 404 Not Found");
    echo json_encode(["error" => "Endpoint not found"]);
}

