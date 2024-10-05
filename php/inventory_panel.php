<?php
session_start();

// Ensure only logged-in users can access this file
if (!isset($_SESSION["user_id"])) {
    header("Location: Login.php");
    exit;
}

// Connect to the database
$mysqli = require __DIR__ . "/Database.php";

// Fetch available products from the inventory table
$sql = "SELECT id, product, quantity FROM inventory";
$result = $mysqli->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Output the products as JSON
header('Content-Type: application/json');
echo json_encode($products);
