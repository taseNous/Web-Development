<?php

// Database connection
$mysqli = require __DIR__ . "/database.php";

// Fetch positions
$sql = 'SELECT id, lat, lon FROM admin_pos';
$result = mysqli_query($mysqli, $sql);

// Fetch all the results as an associative array
$positions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Set the content type to JSON
header('Content-Type: application/json');

// Output the data as JSON
echo json_encode($positions);