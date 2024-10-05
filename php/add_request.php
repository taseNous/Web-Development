<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$mysqli = require __DIR__ . "/Database.php"; 

$user_id = $_SESSION["user_id"];

// Fetch the usertype and ensure the user is a citizen
$sql = "SELECT usertype FROM user WHERE id = ?";
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

// Fetch the corresponding citizen ID from the citizen table
$sql_citizen = "SELECT id FROM citizen WHERE citizen_id = ?";
$stmt_citizen = $mysqli->prepare($sql_citizen);
$stmt_citizen->bind_param("i", $user_id);
$stmt_citizen->execute();
$result_citizen = $stmt_citizen->get_result();
$citizen = $result_citizen->fetch_assoc();

if (!$citizen) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "No matching citizen found for the user"]);
    exit;
}

$citizen_id = $citizen['id']; // The ID in the citizen table

// Check if the category and product combination already exists for this citizen
$sql_check = "SELECT * FROM request WHERE category = ? AND product = ? AND user_id = ?";
$stmt_check = $mysqli->prepare($sql_check);
$stmt_check->bind_param("ssi", $_POST["category"], $_POST["product"], $citizen_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // The category and product combination exists, update the existing record
    $row = $result->fetch_assoc();
    $newQuantity = $row["people"] + $_POST["people"];

    $sql_update = "UPDATE request SET people = ?, when_accepted = ?, when_completed = ? WHERE category = ? AND product = ? AND user_id = ?";
    $stmt_update = $mysqli->prepare($sql_update);

    $stmt_update->bind_param("issssi", $newQuantity, $_POST["when_accepted"], $_POST["when_completed"], $_POST["category"], $_POST["product"], $citizen_id);

    if ($stmt_update->execute()) {
        echo json_encode(["status" => "success", "action" => "update"]);
    } else {
        echo json_encode(["status" => "error", "error" => $mysqli->error]);
    }

} else {
    // The category and product combination does not exist, insert a new record
    $sql_insert = "INSERT INTO request (category, product, people, date_made, when_accepted, when_completed, user_id) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
    $stmt_insert = $mysqli->prepare($sql_insert);

    $stmt_insert->bind_param("ssissi", $_POST["category"], $_POST["product"], $_POST["people"], $_POST["when_accepted"], $_POST["when_completed"], $citizen_id);

    if ($stmt_insert->execute()) {
        echo json_encode(["status" => "success", "action" => "insert"]);
    } else {
        echo json_encode(["status" => "error", "error" => $mysqli->error]);
    }
}