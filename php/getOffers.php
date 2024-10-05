<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

// Connect to your database
$mysqli = require __DIR__ . "/Database.php";

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Step 1: Get the rescuer ID associated with the logged-in user
$sql_rescuer = "SELECT id FROM rescuer WHERE user_id = ?";
$stmt_rescuer = $mysqli->prepare($sql_rescuer);
$stmt_rescuer->bind_param("i", $user_id);
$stmt_rescuer->execute();
$result_rescuer = $stmt_rescuer->get_result();

if ($result_rescuer->num_rows === 0) {
    // No rescuer found for this user
    echo json_encode([]);
    exit;
}

$rescuer = $result_rescuer->fetch_assoc();
$rescuer_id = $rescuer['id'];

$stmt_rescuer->close();

// Step 2: Fetch the requests assigned to this rescuer, including citizen details
$sql_requests = "
    SELECT 
        r.id AS offer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS citizen,
        c.phone,
        r.category,
        r.product,
        r.quantity,
        r.date_made
    FROM 
        offers r
    INNER JOIN 
        citizen c ON r.user_id = c.id
    WHERE 
        r.rescuer_id = ?
";

$stmt_requests = $mysqli->prepare($sql_requests);
$stmt_requests->bind_param("i", $rescuer_id);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();

$requests = [];

while ($row = $result_requests->fetch_assoc()) {
    $requests[] = $row;
}

$stmt_requests->close();
$mysqli->close();

echo json_encode($requests);