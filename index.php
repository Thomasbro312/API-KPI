<?php
// Enable CORS
header('Access-Control-Allow-Origin: http://127.0.0.1:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Check for preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400'); // 1 day
    header("Content-Type: application/json");
    header('Access-Control-Allow-Headers: Api-Key');
    http_response_code(204);
    exit();
}

$expectedToken = 'Help';
$providedToken = $_SERVER['HTTP_API_KEY'] ?? '';

if ($providedToken !== $expectedToken) {
    http_response_code(401);
    exit('Unauthorized');
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestURI = $_SERVER['REQUEST_URI'];

// API endpoints and their corresponding actions
$endpoints = [
    '/api/login' => [
        'POST' => 'login_user',
    ],
    '/api/resource' => [
        'POST' => 'create_user',
    ],
    '/api/campaign/{id}' => [
        'GET' => 'get_campaign_by_id',
        'PUT' => 'update_campaign',
        'DELETE' => 'delete_campaign'
    ],
    '/api/domainlog/{id}' => [
        'GET' => 'domainlog'
    ],
    '/api/campaign' => [
        'POST' => 'create_campaign',
        'GET' => 'get_resource',
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

