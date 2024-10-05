<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$mysqli = require __DIR__ . "/Database.php"; // Adjust this path to your actual Database.php file

// Fetch usertype and citizen_id from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT usertype, id FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['usertype'] !== 'citizen') {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Only citizens can make requests"]);
    exit;
}

$citizen_id = $user['id']; // This is the correct citizen ID to use for the request table