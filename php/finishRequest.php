<?php
session_start();

header('Content-Type: application/json'); // Ensure content type is JSON

function send_json_response($success, $error = null) {
    echo json_encode(["success" => $success, "error" => $error]);
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    send_json_response(false, "User not logged in");
}

$user_id = $_SESSION["user_id"];

// Connect to the database
$mysqli = require __DIR__ . "/Database.php";

if ($mysqli->connect_error) {
    send_json_response(false, "Database connection failed");
}

// Get the request data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

$request_id = $data['id'] ?? null;

if (!$request_id) {
    send_json_response(false, "Request ID is missing");
}

// Step 1: Get the rescuer ID associated with the logged-in user
$sql_rescuer = "SELECT id FROM rescuer WHERE user_id = ?";
$stmt_rescuer = $mysqli->prepare($sql_rescuer);
$stmt_rescuer->bind_param("i", $user_id);
$stmt_rescuer->execute();
$result_rescuer = $stmt_rescuer->get_result();

if ($result_rescuer->num_rows === 0) {
    send_json_response(false, "No rescuer found for this user");
}

$rescuer = $result_rescuer->fetch_assoc();
$rescuer_id = $rescuer['id'];
$stmt_rescuer->close();

// Step 2: Fetch the request details from the database
$request_query = "
    SELECT category, product, people
    FROM request
    WHERE id = ? AND rescuer_id = ?
";
$stmt = $mysqli->prepare($request_query);
$stmt->bind_param("ii", $request_id, $rescuer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    send_json_response(false, "Request not found or unauthorized access");
}

$request = $result->fetch_assoc();
$stmt->close();

// Step 3: Verify that the rescuer has enough of the requested product and quantity in their rescuer_load
$load_check_query = "
    SELECT quantity
    FROM rescuer_load
    WHERE user_id = ? AND category = ? AND product = ?
";
$load_check_stmt = $mysqli->prepare($load_check_query);
$load_check_stmt->bind_param("iss", $rescuer_id, $request['category'], $request['product']);
$load_check_stmt->execute();
$load_result = $load_check_stmt->get_result();

if ($load_result->num_rows === 0) {
    send_json_response(false, "Rescuer does not have the requested product or category in their load");
}

$load = $load_result->fetch_assoc();

if ($load['quantity'] < $request['quantity']) {
    send_json_response(false, "Rescuer does not have enough quantity of the requested product");
}

$load_check_stmt->close();

// Step 4: Update the rescuer_load table
$update_query = "
    UPDATE rescuer_load
    SET quantity = quantity - ?
    WHERE user_id = ? AND category = ? AND product = ?
";
$update_stmt = $mysqli->prepare($update_query);
$update_stmt->bind_param("iiss", $request['quantity'], $rescuer_id, $request['category'], $request['product']);
$update_stmt->execute();

// Step 5: Delete the request from the requests table
$delete_query = "
    DELETE FROM request
    WHERE id = ? AND rescuer_id = ?
";
$delete_stmt = $mysqli->prepare($delete_query);
$delete_stmt->bind_param("ii", $request_id, $rescuer_id);
$delete_stmt->execute();

// Step 6: Cleanup rescuer_load table
if ($update_stmt->affected_rows > 0 && $delete_stmt->affected_rows > 0) {
    $cleanup_query = "
        DELETE FROM rescuer_load
        WHERE user_id = ? AND product = ? AND quantity <= 0
    ";
    $cleanup_stmt = $mysqli->prepare($cleanup_query);
    $cleanup_stmt->bind_param("is", $rescuer_id, $request['product']);
    $cleanup_stmt->execute();
    $cleanup_stmt->close();

    send_json_response(true);
} else {
    send_json_response(false, "Failed to finish the request");
}

$update_stmt->close();
$delete_stmt->close();
$mysqli->close();