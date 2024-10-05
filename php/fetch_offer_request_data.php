<?php
$mysqli = require __DIR__ . "/Database.php";

// Fetch the offer and request data grouped by date
$sql = "
    SELECT DATE(date_made) AS date, COUNT(*) AS total, 'offer' AS type
    FROM offers
    GROUP BY DATE(date_made)
    UNION ALL
    SELECT DATE(when_accepted) AS date, COUNT(*) AS total, 'request' AS type
    FROM request
    GROUP BY DATE(when_accepted)
    ORDER BY date;
";

$result = $mysqli->query($sql);

// Prepare the data for JSON output
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Send the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
