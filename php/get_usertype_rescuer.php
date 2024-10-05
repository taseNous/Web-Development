<?php
session_start();
$mysqli = require __DIR__ . "/database.php";
$user_id = $_SESSION["user_id"];
$sql = "SELECT usertype FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($usertype);
$stmt->fetch();
$stmt->close();

echo json_encode(['usertype' => $usertype]);