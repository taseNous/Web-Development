<?php
session_start();

$mysqli = require __DIR__ . "/Database.php"; // Database connection

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

// Get the list of products to transfer from the request
$data = json_decode(file_get_contents('php://input'), true);
$products = $data['products'];

if (empty($products)) {
    echo json_encode(['success' => false, 'message' => 'No products selected']);
    exit;
}

try {
    $mysqli->begin_transaction();

    foreach ($products as $product) {
        $product_id = $product['id'];
        $quantity = $product['quantity'];

        // Check if the product exists in inventory with enough quantity
        $stmt = $mysqli->prepare("SELECT quantity FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($available_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($available_quantity < $quantity) {
            throw new Exception("Not enough quantity in inventory for product ID: " . $product_id);
        }

        // Insert the product into rescuer_load
        $stmt = $mysqli->prepare("INSERT INTO rescuer_load (user_id, category, product, volume, weight, pack_size, type, size, quantity) 
                                  SELECT ?, category, product, volume, weight, pack_size, type, size, ? FROM inventory WHERE id = ?");
        $stmt->bind_param("isi", $rescuer_id, $quantity, $product_id);
        $stmt->execute();

        // Update the inventory table to reduce the quantity
        $stmt = $mysqli->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();

        // Remove the item from the inventory if quantity reaches zero
        $stmt = $mysqli->prepare("DELETE FROM inventory WHERE id = ? AND quantity = 0");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    }

    $mysqli->commit();
    echo json_encode(['success' => true, 'message' => 'Selected products successfully transferred to rescuer load']);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
}
