<?php
session_start();

header('Content-Type: application/json'); // Ensure the response is JSON

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// Return the logged-in user's ID as a JSON response
echo json_encode(["user_id" => $_SESSION["user_id"]]);
