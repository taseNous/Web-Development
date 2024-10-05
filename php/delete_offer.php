<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

$mysqli = require __DIR__ . "/database.php";

// Check if the offer ID is provided
if (isset($_POST['offer_id'])) {
    $offer_id = $_POST['offer_id'];
    $user_id = $_SESSION["user_id"];

    // Check if the offer belongs to the logged-in user
    $sql = "DELETE FROM offers WHERE id = ? AND user_id = (SELECT id FROM citizen WHERE citizen_id = ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $offer_id, $user_id);

    if ($stmt->execute()) {
        // Redirect back to the announcement page or send a success response
        header("Location: ../pages/announcement.php");
        exit;
    } else {
        echo "Error: Could not delete the offer.";
    }

    $stmt->close();
}

$mysqli->close();