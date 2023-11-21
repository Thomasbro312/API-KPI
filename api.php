<?php
use Firebase\JWT\JWT;
// api.php

include 'config/config.php';

// This gets all the campaigns from the database
function get_resource() {
    global $conn;

    $sql = "SELECT * FROM campaigns";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        echo json_encode($resources);
    } else {
        return;
    }
}

//This creates a user with data from the vue application

function domainlog($matches) {
    global $conn;
    $campaign_id = $matches[1];
    $sql = "SELECT * FROM domainlog WHERE campaign_id = '$campaign_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];

        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        // Encode the result as JSON and return it
        echo json_encode($resources);
    } else {
        // Return 'empty' as a JSON-encoded string
        echo json_encode(['message' => 'empty']);
    }
}

function create_user() {
    global $conn;

    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];
    $user_name = $data['name'];
    $phone_number = $data['phone_number'];
    $company_name = $data['company_name'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT );
    $role = '1'; // Default value = 'planning'

    $sql = "INSERT INTO users (email, password, user_name, phone_number, company_name, role) VALUES ( '$email','$password', '$user_name', '$phone_number', '$company_name', '$role')";
    if ($conn->query($sql) === TRUE) {
        return ["message" => "Resource created successfully"];
    } else {
        return ["error" => "Error creating resource: " . $conn->error];
    }
}

function create_log() {
    // Implement logic to create a resource
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    // Implement logic to create a resource in the database

    $domain_name = $data['domain_name'];
    $page_name = $data['page_name'];
    $campaign_id = $data['campaign_id'];
    $session_id = rand(1000000,9999999);
    $device = $data['device'];
    $device_os = $data['device_os'];
    $device_browser = $data['device_browser'];
    $datetimestamp = $data['datetimestamp'];



    $sql = "INSERT INTO domainlog (domain_name, campaign_id, session_id, page_name, device, device_os, device_browser, datetimestamp) VALUES ('$domain_name', '$campaign_id', '$page_name', '$session_id', '$device', '$device_os', '$device_browser', '$datetimestamp')";
    if ($conn->query($sql) === TRUE) {
        return ["message" => "Resource created successfully"];
    } else {
        return ["error" => "Error creating resource: " . $conn->error];
    }
}

//This creates a campaign with data from the vue application.
function create_campaign() {
    // Implement logic to create a resource
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    // Implement logic to create a resource in the database
    $campaign_id = 'imagine_campaign_'  . rand(1000000,9999999);
    $client_id= $data['client_id'];
    $campaign_name = $data['campaign_name'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $start_date_diff = new DateTime($data['start_date']);
    $end_date_diff =  new DateTime($data['end_date']);
    $diff_date = $end_date_diff->diff($start_date_diff)->format('%a');
    $campaign_phase = '1';

    $sql = "INSERT INTO campaigns (campaign_id, campaign_name, client_id, start_date, end_date, diff_date, campaign_phase) VALUES ('$campaign_id', '$campaign_name', '$client_id', '$start_date', '$end_date', '$diff_date', '$campaign_phase')";
    if ($conn->query($sql) === TRUE) {
        return ["message" => "Resource created successfully"];
    } else {
        return ["error" => "Error creating resource: " . $conn->error];
    }
}

//this gets a campaign by the id that is requested
function get_campaign_by_id($matches) {
    global $conn;
    $campaign_id = $matches[1];
    $sql = "SELECT * FROM campaigns WHERE id = '$campaign_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];

        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        // Encode the result as JSON and return it
        echo json_encode($resources);
    } else {
        // Return 'empty' as a JSON-encoded string
        echo json_encode(['message' => 'empty']);
    }
}


//this updates the selected campaign with data from the vue
function update_campaign($matches) {
    // Implement logic to update a resource
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    $resource_id = $matches[1];

    $campaign_name = $data['campaign_name'];
    $client_id = $data['client_id'] + 0;
    $start_date = $data['start_date'];
    $end_date =  $data['end_date'];
    $start_date_diff = new DateTime($data['start_date']);
    $end_date_diff =  new DateTime($data['end_date']);
    $diff_date = $end_date_diff->diff($start_date_diff)->format('%a');
    $campaign_phase = $data['campaign_phase'] + 0;

    $sql = "UPDATE campaigns SET campaign_name = '$campaign_name', client_id = '$client_id', start_date = '$start_date', end_date = '$end_date', diff_date = '$diff_date', campaign_phase = '$campaign_phase' WHERE id = $resource_id";
    if ($conn->query($sql) === TRUE) {
        return ["message" => "Resource updated successfully"];
    } else {
        return ["error" => "Error updating resource: " . $conn->error];
    }
}

function update_user_client($matches) {
    // Implement logic to update a resource
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    $resource_id = $matches[1];

    $email = $data['email'];
    $phone_number = $data['phone_number'];
    $company_name = $data['company_name'];
    $user_name = $data['user_name'];

    $sql = "UPDATE users SET email = '$email', phone_number = '$phone_number', company_name = '$company_name', user_name = '$user_name' WHERE user_id = $resource_id";
    if ($conn->query($sql) === TRUE) {
        return ["message" => "Resource updated successfully"];
    } else {
        return ["error" => "Error updating resource: " . $conn->error];
    }
}

//This deletes the specified campaign
function delete_campaign($matches) {
    global $conn;
    $campaign_id = $matches[1];
    $sql = "DELETE FROM campaigns WHERE id = '$campaign_id'";

    if ($conn->query($sql) === TRUE) {
        return ["message" => "Resource deleted successfully"];
    } else {
        return ["error" => "Error deleting resource: " . $conn->error];
    }
}

// this gets all the necessary user data for selections boxes and putting a name down
function user_info()
{
    global $conn;
    $sql = "SELECT user_id, user_name, company_name, phone_number, role FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        echo json_encode($resources);
    } else {
        echo json_encode(['message' => 'empty']);
    }
}

// gets phase data so that the vue app can identify which phase is what
function phase_info()
{
    global $conn;
    $sql = "SELECT id, phase_name FROM phasestatus";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        echo json_encode($resources);
    } else {
        echo json_encode(['message' => 'empty']);
    }
}

// this gets userinfo with a provided id
function user_info_by_id($matches)
{
    global $conn;
    $user_id = $matches[1];

    $sql = "SELECT user_id, user_name, email, phone_number, company_name FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        echo json_encode($resources);
    } else {
        echo json_encode(['message' => 'empty']);
    }
}

// this get campaigns with the specific client id
function campaigns_by_client_id($matches)
{
    global $conn;
    $user_id = $matches[1];

    $sql = "SELECT * FROM campaigns WHERE client_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        echo json_encode($resources);
    } else {
        json_encode(['message' => 'empty']);
    }
}

// this gets the phase with the provided id
function phase_by_id($matches)
{
    global $conn;
    $phase_id = $matches[1];

    $sql = "SELECT phase_name FROM phasestatus WHERE id = '$phase_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resources = [];
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        echo json_encode($resources);
    } else {
        echo json_encode(['message' => 'empty']);
    }
}

// this allows the user to login after it is confirmed with the correct password
function login_user()
{
    global $conn;

    $requestData = json_decode(file_get_contents('php://input'), true);
    if (!$requestData || !isset($requestData['email']) || !isset($requestData['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid request']);
        exit;
    }

    $email = $requestData['email'];
    $password = $requestData['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $storedPassword = $user['password'];

        // Verify the password using password_verify (assuming you stored hashed passwords)
        if (password_verify($password, $storedPassword)) {
            $token = generateToken($user);
            echo json_encode(['user' => $user, 'token' => $token]);

        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Authentication failed']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'User not found']);
    }
}

// this generates the token for the login system
function generateToken($user)
{

    require 'vendor/autoload.php'; // Load the JWT library

// Your secret key for signing the JWT
    $secret_key = 'Ksajsaddhkbbdwsakhbdasjkbsdajknhsdajknh6718736278hgdsahjdgshja2378167821';

// User data or claims to include in the JWT payload
    $user_id = $user['user_id'];
    $email =   $user['email'];

    $payload = array(
        "user_id" => $user_id,
        "email" => $email,
    );

    $expiration_time = time() + 3600;
    $payload["exp"] = $expiration_time;

// Generate the JWT token
    $jwt = JWT::encode($payload, $secret_key, 'HS256');

    $payload = [
        $jwt
    ];

    // Encode the payload and return the token
    return json_encode($payload);
}
