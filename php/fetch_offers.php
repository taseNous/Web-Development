<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$mysqli = require __DIR__ . "/../php/database.php";

$user_id = $_SESSION["user_id"];

// Get citizen ID for the logged-in user
$sql_citizen = "SELECT id FROM citizen WHERE citizen_id = ?";
$stmt_citizen = $mysqli->prepare($sql_citizen);
$stmt_citizen->bind_param("i", $user_id);
$stmt_citizen->execute();
$result_citizen = $stmt_citizen->get_result();
$citizen = $result_citizen->fetch_assoc();
$citizen_id = $citizen['id'];

// Fetch all offers made by the logged-in user
$sql_offers = "SELECT id, category, product, quantity FROM offers WHERE user_id = ?";
$stmt_offers = $mysqli->prepare($sql_offers);
$stmt_offers->bind_param("i", $citizen_id);
$stmt_offers->execute();
$result_offers = $stmt_offers->get_result();

$offers = [];
while ($offer = $result_offers->fetch_assoc()) {
    $offers[] = $offer;
}

$stmt_offers->close();

echo json_encode(['success' => true, 'offers' => $offers]);