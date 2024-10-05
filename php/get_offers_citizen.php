<?php

// Database connection
$mysqli = require __DIR__ . "/database.php";

// Fetch positions of citizens who made offers
$sql = '
    SELECT c.first_name, c.last_name, c.phone, c.latitude, c.longitude, r.id, r.rescuer_id, r.product, r.quantity
    FROM citizen c
    INNER JOIN offers r ON c.id = r.user_id
';

// Execute the query
$result = mysqli_query($mysqli, $sql);

// Fetch all the results as an associative array
$positions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Set the content type to JSON
header('Content-Type: application/json');

// Output the data as JSON
echo json_encode($positions);