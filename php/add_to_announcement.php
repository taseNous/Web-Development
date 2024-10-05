<?php
session_start();

// Check if the user is logged in by verifying the session
if (!isset($_SESSION["user_id"])) {
    // User is not logged in, redirect to the login page
    header("Location: ../index.php");
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION["user_id"];

// Connect to the database
$mysqli = require __DIR__ . "/database.php";

// Check for database connection errors
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Check if the user has the usertype 'admin'
$stmt = $mysqli->prepare("SELECT usertype FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($usertype);
$stmt->fetch();
$stmt->close();

if ($usertype !== 'admin') {
    echo "Error: Only admin users can make announcements.";
    exit;
}

// Retrieve category and product from the POST request
$category = $_POST['category'];
$product = $_POST['product'];

// Check if the product already exists in the announcements table
$check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM announcement WHERE category = ? AND product = ?");
if (!$check_stmt) {
    die('Prepare failed: ' . $mysqli->error);
}
$check_stmt->bind_param("ss", $category, $product);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    echo "The product already exists in this category.";
    exit;
}

// Prepare the SQL query to insert the new announcement
$stmt = $mysqli->prepare("INSERT INTO announcement (user_id, category, product) VALUES (?, ?, ?)");
if (!$stmt) {
    die('Prepare failed: ' . $mysqli->error);
}

// Bind the parameters
if (!$stmt->bind_param("iss", $user_id, $category, $product)) {
    die('Binding parameters failed: ' . $stmt->error);
}

// Execute the query
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

echo "Success: Announcement inserted.";

// Close the statement and connection
$stmt->close();
$mysqli->close();