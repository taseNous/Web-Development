<?php

if (empty($_POST["username"])) { // This checks if the "username" field is empty. If it is, the script terminates with an error message.
    die("Username is required");
}

if (strlen($_POST["password"]) < 8) { // Checks if the password is at least 8 characters long. If not, the script terminates.
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) { // Ensures the password contains at least one letter. This uses a regular expression, where "/[a-z]/i" checks for any alphabetic character (case-insensitive).
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) { // Ensures the password contains at least one number using a regular expression ("/[0-9]/").
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) { // Verifies if the password and password confirmation fields match. If they don't, the script terminates.
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hashes the password using the `password_hash()` function, which securely encrypts it. `PASSWORD_DEFAULT` ensures that the strongest available hashing algorithm is used.

$mysqli = require __DIR__ . "/database.php";

$sql_user = "INSERT INTO user (username, password_hash) VALUES (?, ?)"; // Prepares an SQL query to insert the username and hashed password into the `user` table. The `?` placeholders will be replaced with actual data later.
 
$stmt_user = $mysqli->stmt_init(); // Initializes a prepared statement object for secure querying.

if (!$stmt_user->prepare($sql_user)) { // Prepares the SQL query for execution. If there's an error (e.g., a syntax error), the script terminates.
    die("SQL error: " . $mysqli->error);
}

$stmt_user->bind_param("ss", $_POST["username"], $password_hash);

if (!$stmt_user->execute()) { // Executes the statement. If there's an error (e.g., the username is already taken, which triggers error code 1062).
    if ($mysqli->errno === 1062) {
        die("Username already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}

$user_id = $stmt_user->insert_id; // Retrieve the `user_id` of the newly created user

$sql_citizen = "INSERT INTO citizen (citizen_id, first_name, last_name, phone, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)"; // Insert into the `citizen` table

$stmt_citizen = $mysqli->stmt_init();

if (!$stmt_citizen->prepare($sql_citizen)) {
    die("SQL error: " . $mysqli->error);
}

$stmt_citizen->bind_param("isssss", $user_id, $_POST["first_name"], $_POST["last_name"], $_POST["phone"], $_POST["latitude"], $_POST["longitude"]);

if ($stmt_citizen->execute()) { // Executes the statement to insert data into the `citizen` table. If the operation is successful, the user is redirected to the homepage (index.php).
    header("Location: ../index.php");
    exit;
} else {
    die($mysqli->error . " " . $mysqli->errno); // If there's an error during the execution, the script terminates and outputs the error message and code.
}