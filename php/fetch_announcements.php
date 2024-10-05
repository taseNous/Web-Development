<?php
// fetch_announcements.php

session_start();

$mysqli = require __DIR__ . "/database.php"; // Include database connection

// Prepare SQL query to get data from the 'announcement' table
$sql = "SELECT id, category, product FROM announcement WHERE citizen_id IS NULL";
$result = $mysqli->query($sql);

$announcements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Return the result as a JSON object
echo json_encode([
    'success' => true,
    'announcements' => $announcements
]);