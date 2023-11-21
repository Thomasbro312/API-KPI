<?php
// api/login.php

// Include your database connection here (as shown in the previous PHP example)

include 'config/config.php';


// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $conn;
    $data = json_decode(file_get_contents('php://input'));

    if (isset($data->emailAdress) && isset($data->password)) {
        $emailAdress = $data->emailAdress;
        $password = $data->password;

        // Query the database to check if the user exists
        $sql = "SELECT * FROM users WHERE emailAdress = '$emailAdress' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $storedPassword = $user['password'];

            // Verify the password using password_verify (assuming you stored hashed passwords)
            if (password_verify($password, $storedPassword)) {
                // Authentication successful
                $response = ["message" => "Login successful"];
                http_response_code(200);
            } else {
                // Incorrect password
                $response = ["error" => "Incorrect password"];
                http_response_code(401);
            }
        } else {
            // User not found
            $response = ["error" => "User not found"];
            http_response_code(404);
        }

        echo json_encode($response);
    } else {
        // Missing username or password
        $response = ["error" => "Missing username or password"];
        http_response_code(400);
        echo json_encode($response);
    }
} else {
    // Handle other HTTP methods if needed
    http_response_code(405);
}
