<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Connect to your database
$mysqli = require __DIR__ . "/Database.php";

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get the offer ID from the POST request
$requestData = json_decode(file_get_contents('php://input'), true);
$offer_id = $requestData['offer_id'] ?? null;

if (!$offer_id) {
    echo json_encode(["success" => false, "error" => "Offer ID is missing"]);
    exit;
}

// Step 1: Get the rescuer ID associated with the logged-in user
$sql_rescuer = "SELECT id FROM rescuer WHERE user_id = ?";
$stmt_rescuer = $mysqli->prepare($sql_rescuer);
$stmt_rescuer->bind_param("i", $user_id);
$stmt_rescuer->execute();
$result_rescuer = $stmt_rescuer->get_result();

if ($result_rescuer->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "No rescuer found for this user"]);
    exit;
}

$rescuer = $result_rescuer->fetch_assoc();
$rescuer_id = $rescuer['id'];

$stmt_rescuer->close();

// Step 2: Update the offer table to set the rescuer_id to NULL for the specified offer
$sql_update = "UPDATE offers SET rescuer_id = NULL WHERE id = ? AND rescuer_id = ?";
$stmt_update = $mysqli->prepare($sql_update);
$stmt_update->bind_param("ii", $offer_id, $rescuer_id);
$stmt_update->execute();

if ($stmt_update->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Error: No rows updated or offer does not belong to this rescuer"]);
}

$stmt_update->close();
$mysqli->close();