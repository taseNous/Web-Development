<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Database connection
$mysqli = require __DIR__ . "/Database.php";

// Get the request data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

// Get the request_id and the logged-in user's ID
$request_id = $data['id'];
$user_id = $_SESSION['user_id']; // The logged-in user's ID

// Debug: Log the received request_id and user_id
error_log("Received Request ID: " . $request_id);
error_log("Logged-in User ID: " . $user_id);

// Step 1: Retrieve the rescuer.id based on the user_id
$sql = "SELECT id FROM rescuer WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($rescuer_id);
$stmt->fetch();
$stmt->close();

// Debug: Log the retrieved rescuer_id
if (!$rescuer_id) {
    error_log("No rescuer profile found for user_id: " . $user_id);
    echo json_encode(['success' => false, 'error' => 'No rescuer profile found for this user']);
    exit;
} else {
    error_log("Retrieved Rescuer ID: " . $rescuer_id);
}

// Step 2: Update the request table with the rescuer_id
$sql = "UPDATE request SET rescuer_id = ?, when_accepted = NOW() WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $rescuer_id, $request_id);

if ($stmt->execute()) {
    // Debug: Log how many rows were affected
    error_log("Affected Rows: " . $stmt->affected_rows);

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'This request is already taken.']);
    }
} else {
    // Debug: Log the error message if the query fails
    error_log("MySQL Error: " . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'Failed to take on request']);
}

$stmt->close();
$mysqli->close();