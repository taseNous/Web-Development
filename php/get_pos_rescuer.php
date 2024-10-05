<?php
session_start();

$mysqli = require __DIR__ . "/database.php";
$user_id = $_SESSION["user_id"];
$usertype = $_SESSION["usertype"];

if ($usertype === "rescuer") {
    // Fetch only the logged-in rescuer's position
    $sql = "
        SELECT c.username, r.id, r.status, r.has_tasks, r.lat, r.lon 
        FROM user c
        INNER JOIN rescuer r ON c.id = r.user_id
        WHERE r.user_id = ?
    ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $positions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Fetch all rescuer positions (for admin)
    $sql = "
        SELECT c.username, r.id, r.status, r.has_tasks, r.lat, r.lon 
        FROM user c
        INNER JOIN rescuer r ON c.id = r.user_id
    ";
    $result = $mysqli->query($sql);
    $positions = $result->fetch_all(MYSQLI_ASSOC);
}

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode($positions);
