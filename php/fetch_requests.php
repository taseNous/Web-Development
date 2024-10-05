<?php
$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT category, product, people FROM request";
$result = $mysqli->query($sql);

if ($result) {
    $requests = [];

    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    echo json_encode($requests);
} else {
    echo json_encode(["error" => $mysqli->error]);
}