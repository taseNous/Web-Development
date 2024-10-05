<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Include your database connection
$mysqli = require __DIR__ . "/Database.php";

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

// Debug: Check the received data
file_put_contents('php://stderr', print_r($data, true));

if (isset($data['id']) && isset($data['lat']) && isset($data['lon'])) {
    $id = intval($data['id']);
    $lat = floatval($data['lat']);
    $lon = floatval($data['lon']);

    // Prepare the SQL statement to update the position
    $stmt = $mysqli->prepare("UPDATE admin_pos SET lat = ?, lon = ? WHERE id = ?");
    $stmt->bind_param("ddi", $lat, $lon, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}

$mysqli->close();