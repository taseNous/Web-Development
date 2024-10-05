<?php

if (empty($_POST["username"])) {
    die("Username is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Validate latitude and longitude
if (!isset($_POST["lat"]) || !is_numeric($_POST["lat"])) {
    die("Latitude is required and must be a number");
}

if (!isset($_POST["lon"]) || !is_numeric($_POST["lon"])) {
    die("Longitude is required and must be a number");
}

$lat = $_POST["lat"];
$lon = $_POST["lon"];
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

// Insert into the `user` table with usertype set to "rescuer"
$sql_user = "INSERT INTO user (username, password_hash, usertype) VALUES (?, ?, 'rescuer')";

$stmt_user = $mysqli->stmt_init();

if (!$stmt_user->prepare($sql_user)) {
    die("SQL error: " . $mysqli->error);
}

$stmt_user->bind_param("ss", $_POST["username"], $password_hash);

if (!$stmt_user->execute()) {
    if ($mysqli->errno === 1062) {
        die("Username already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}

// Get the last inserted user ID
$user_id = $mysqli->insert_id;

// Insert into the `rescuer_pos` table
$sql_rescuer_pos = "INSERT INTO rescuer (user_id, lat, lon) VALUES (?, ?, ?)";

$stmt_rescuer_pos = $mysqli->stmt_init();

if (!$stmt_rescuer_pos->prepare($sql_rescuer_pos)) {
    die("SQL error: " . $mysqli->error);
}

$stmt_rescuer_pos->bind_param("idd", $user_id, $lat, $lon);

if (!$stmt_rescuer_pos->execute()) {
    die("Error inserting rescuer position: " . $mysqli->error);
}

// Redirect to profile page on success
header("Location: ../pages/profile.php");
exit;