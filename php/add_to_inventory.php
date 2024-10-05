<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    
    header("Location: ../index.php?showLogin=true");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $category = $_POST["category"];
    $product = $_POST["product"];
    $volume = isset($_POST['volume']) ? $_POST['volume'] : null;
    $weight = isset($_POST['weight']) ? $_POST['weight'] : null;
    $pack_size = isset($_POST['pack_size']) ? $_POST['pack_size'] : null;
    $type = isset($_POST['type']) ? $_POST['type'] : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;
    $quantity = $_POST["quantity"];

    // Validate the received data
    if (!$category || !$product || !$quantity) {
        echo "Error: Missing required fields";
        exit;
    }

    $sql_check = "SELECT quantity FROM inventory WHERE category = ? AND product = ?";
    $stmt_check = $mysqli->stmt_init();

    if (!$stmt_check->prepare($sql_check)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt_check->bind_param("ss", $category, $product);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
    
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;

        $sql_update = "UPDATE inventory SET quantity = ?, volume = ?, weight = ?, pack_size = ?, type = ?, size = ? WHERE category = ? AND product = ?";
        $stmt_update = $mysqli->stmt_init();

        if (!$stmt_update->prepare($sql_update)) {
            die("SQL error: " . $mysqli->error);
        }

        $stmt_update->bind_param("isssssss", $new_quantity, $volume, $weight, $pack_size, $type, $size, $category, $product);

        if ($stmt_update->execute()) {
            echo json_encode(["status" => "success", "action" => "update", "category" => $category, "product" => $product, "quantity" => $new_quantity]);
        } else {
            echo json_encode(["status" => "error", "error" => $mysqli->error]);
        }

    } else {
        
        $sql_insert = "INSERT INTO inventory (category, product, volume, weight, pack_size, type, size, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $mysqli->stmt_init();

        if (!$stmt_insert->prepare($sql_insert)) {
            die("SQL error: " . $mysqli->error);
        }

        $stmt_insert->bind_param("sssssssi", $category, $product, $volume, $weight, $pack_size, $type, $size, $quantity);

        if ($stmt_insert->execute()) {
            echo json_encode(["status" => "success", "action" => "insert", "category" => $category, "product" => $product, "quantity" => $quantity]);
        } else {
            echo json_encode(["status" => "error", "error" => $mysqli->error]);
        }
    }

    $stmt_check->close();
    if (isset($stmt_update)) {
        $stmt_update->close();
    }
    if (isset($stmt_insert)) {
        $stmt_insert->close();
    }

    $mysqli->close();
} else {
    echo json_encode(["status" => "error", "error" => "Invalid request method"]);
}