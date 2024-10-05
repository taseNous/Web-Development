<?php
// fetch_inventory.php

session_start();

$mysqli = require __DIR__ . "/../php/database.php";

// Fetch data from the 'inventory' table
$sql = "SELECT category, product, volume, weight, pack_size, type, size, quantity FROM inventory";
$result = $mysqli->query($sql);

$inventory = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
}

// Return the result as a JSON object
echo json_encode([
    'success' => true,
    'inventory' => $inventory
]);