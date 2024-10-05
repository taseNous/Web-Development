<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Connect to the database
$mysqli = require __DIR__ . "/Database.php";

// Check connection
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Set Content-Type header for JSON response
header('Content-Type: application/json');

// Get the request data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

$offer_id = $data['id'] ?? null;

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

// Step 2: Fetch the offer details from the database
$offer_query = "
    SELECT category, product, quantity
    FROM offers
    WHERE id = ? AND rescuer_id = ?
";
$stmt = $mysqli->prepare($offer_query);
$stmt->bind_param("ii", $offer_id, $rescuer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "Offer not found or unauthorized access"]);
    exit;
}

$offer = $result->fetch_assoc();

// Step 3: Insert the product and quantity into the rescuer_load table
$insert_query = "
    INSERT INTO rescuer_load (user_id, category, product, quantity)
    VALUES (?, ?, ?, ?)
";
$insert_stmt = $mysqli->prepare($insert_query);
$insert_stmt->bind_param("issi", $rescuer_id, $offer['category'], $offer['product'], $offer['quantity']);
$insert_stmt->execute();

// Step 4: Delete the offer from the offers table
$delete_query = "
    DELETE FROM offers
    WHERE id = ? AND rescuer_id = ?
";
$delete_stmt = $mysqli->prepare($delete_query);
$delete_stmt->bind_param("ii", $offer_id, $rescuer_id);
$delete_stmt->execute();

// Step 5: Check if the operations were successful
if ($insert_stmt->affected_rows > 0 && $delete_stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to finish the offer"]);
}

// Close statements and the database connection
$stmt->close();
$insert_stmt->close();
$delete_stmt->close();
$mysqli->close();
