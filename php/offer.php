<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$mysqli = require __DIR__ . "/database.php";
$user_id = $_SESSION["user_id"];

$sql = "SELECT usertype FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['usertype'] !== 'citizen') {
    http_response_code(403);
    echo json_encode(["error" => "Only citizens can take on offers"]);
    exit;
}

$sql_citizen = "SELECT id FROM citizen WHERE citizen_id = ?";
$stmt_citizen = $mysqli->prepare($sql_citizen);
$stmt_citizen->bind_param("i", $user_id);
$stmt_citizen->execute();
$result_citizen = $stmt_citizen->get_result();
$citizen = $result_citizen->fetch_assoc();

if (!$citizen) {
    http_response_code(400);
    echo json_encode(["error" => "No matching citizen found for the user"]);
    exit;
}

$citizen_id = $citizen['id'];

// Check if category, product, and quantity are provided
if (!isset($_POST['category']) || !isset($_POST['product']) || !isset($_POST['quantity'])) {
    http_response_code(400);
    echo json_encode(["error" => "All fields are required (category, product, quantity)"]);
    exit;
}

$category = $_POST['category'];
$product = $_POST['product'];
$quantity = $_POST['quantity'];
$announcement_id = $_POST['announcement_id'];

// Validate quantity is a positive integer
if (!is_numeric($quantity) || (int)$quantity <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Quantity must be a positive number"]);
    exit;
}

// Check if the category and product combination already exists in the offers table for this citizen
$sql_check = "SELECT * FROM offers WHERE category = ? AND product = ? AND user_id = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("ssi", $category, $product, $citizen_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "error" => "You have already taken on this offer"]);
} else {
    // Insert new offer into the offers table
    $sql_insert = "INSERT INTO offers (user_id, announcement_id, category, product, quantity, date_made) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt_insert = $mysqli->prepare($sql_insert);
    $stmt_insert->bind_param("iisss", $citizen_id,$announcement_id, $category, $product, $quantity);

    

    if ($stmt_insert->execute()) {
        // Update the announcement table with the citizen_id
        $sql_update_announcement = "UPDATE announcement SET citizen_id = ? WHERE id = ?";
        $stmt_update_announcement = $mysqli->prepare($sql_update_announcement);
        $stmt_update_announcement->bind_param("ii", $citizen_id, $announcement_id);
        $stmt_update_announcement->execute();
    
        echo json_encode(["status" => "success", "action" => "insert"]);
    } else {
        echo json_encode(["status" => "error", "error" => $mysqli->error]);
    }
}

$mysqli->close();