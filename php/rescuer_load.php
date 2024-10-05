<?php
session_start();


// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$mysqli = require __DIR__ . "/Database.php";
$user_id = $_SESSION["user_id"];

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

// Step 2: Fetch data from rescuer_load table
$sql = "SELECT * FROM rescuer_load WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $rescuer_id);
$stmt->execute();
$result = $stmt->get_result();

$rescuer_loads = [];
while ($row = $result->fetch_assoc()) {
    $rescuer_loads[] = $row;
}

// Output the JSON data
echo json_encode($rescuer_loads);