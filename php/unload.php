<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$mysqli = require __DIR__ . "/database.php"; // Database connection

$user_id = $_SESSION["user_id"];

// Fetch the rescuer's id using the user_id
$stmt = $mysqli->prepare("SELECT id FROM rescuer WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($rescuer_id);
$stmt->fetch();
$stmt->close();

if (!$rescuer_id) {
    echo json_encode(['success' => false, 'message' => 'Rescuer not found for user_id: ' . $user_id]);
    exit;
}

try {
    $mysqli->begin_transaction();

    // Select all items from rescuer_load for this rescuer
    $stmt = $mysqli->prepare("SELECT category, product, volume, weight, pack_size, type, size, quantity FROM rescuer_load WHERE user_id = ?");
    $stmt->bind_param("i", $rescuer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        $product = $row['product'];
        $volume = $row['volume'];
        $weight = $row['weight'];
        $pack_size = $row['pack_size'];
        $type = $row['type'];
        $size = $row['size'];
        $quantity = $row['quantity'];

        // Insert into inventory or update if the product already exists
        $stmt = $mysqli->prepare("INSERT INTO inventory (category, product, volume, weight, pack_size, type, size, quantity) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
        $stmt->bind_param("sssssssi", $category, $product, $volume, $weight, $pack_size, $type, $size, $quantity);
        $stmt->execute();
    }

    // Remove all items from rescuer_load for this rescuer
    $stmt = $mysqli->prepare("DELETE FROM rescuer_load WHERE user_id = ?");
    $stmt->bind_param("i", $rescuer_id);
    $stmt->execute();

    $mysqli->commit();
    echo json_encode(['success' => true, 'message' => 'Data successfully unloaded and moved back to inventory']);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
}