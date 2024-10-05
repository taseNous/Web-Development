<?php

session_start(); // Starts the session to track the user's session data (like user_id, usertype, etc.)

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    // If not logged in or not an admin, redirect to the homepage (../index.php)
    header("Location: ../index.php");
    exit; // Make sure the script stops executing after the redirect
}

$mysqli = require __DIR__ . "/../php/database.php"; // Include database connection
$user_id = $_SESSION["user_id"]; // Store the user ID from the session

// Prepare an SQL statement to get the usertype from the database based on the user ID
$sql = "SELECT usertype FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql); // Prepare the SQL statement
$stmt->bind_param("i", $user_id); // Bind the user_id (integer) to the SQL query
$stmt->execute(); // Execute the query
$stmt->bind_result($usertype); // Bind the result to the $usertype variable
$stmt->fetch(); // Fetch the result

$stmt->close(); // Close the statement

// Update the session with the latest usertype
$_SESSION['usertype'] = $usertype;

?> 

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Include external CSS for styling -->
</head>

<body>

    <nav class="navbar">
        <?php include '../includes/navbar.php'; ?> <!-- Include the navbar from an external file -->
    </nav>
    
    <br>

    <!-- Form for adding new items to the inventory -->
    <div class="announcement-admin">
        <br>
        <br>

        <div class="announcement-header">
            <h3>Add product to Inventory</h3>
        </div>
        <div class="announcement-form-admin">
            <form action="../php/add_to_inventory.php" method="post" id="addItemForm" novalidate>

                <!-- Dropdown to select product category -->
                <div class="form-group">
                    <label for="productCategory">Category</label>
                    <select id="productCategory" class="form-control">
                        <!-- Categories will be populated here -->
                    </select>
                </div>
                
                <!-- Dropdown to select product name, will be populated based on category -->
                <div class="form-group">
                    <label for="productName">Product</label>
                    <select id="productName" class="form-control">
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>

                <!-- Input field for the product quantity -->
                <div class="form-group">
                    <label for="productQuantity">Quantity</label>
                    <input type="number" id="productQuantity" class="form-control" placeholder="Enter quantity">
                </div>
                
                <!-- Submit button to add the item to the inventory -->
                <button type="submit" class="btn btn-primary">Add to Inventory</button>

            </form>
        </div>
    </div>

    <!-- Section to display the inventory status in a table format -->
    <div class="inventory-status">
        <h3>Inventory Status</h3>
        <!-- Dropdown to select product category -->
        <div class="form-group">
                    <label for="productCategory1">Category</label>
                    <select id="productCategory1" class="form-control">
                        <!-- Categories will be populated here -->
                    </select>
                </div>
        <table id="inventoryTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Volume</th>
                    <th>Weight</th>
                    <th>Pack Size</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically inserted here by JavaScript -->
            </tbody>
        </table>
    </div>

    <footer class="row">
        <?php include '../includes/footer.php'; ?> <!-- Include the footer from an external file -->
    </footer>

</body>
</html>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> <!-- Old jQuery version -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Modern jQuery version -->
<script src="../js/inventory10.js"></script> <!-- Custom script for inventory management -->
<script src="../js/fetch_inventory.js"></script> <!-- Script for fetching inventory data -->
<script src="../js/drop2.js"></script> <!-- Script for handling dropdowns -->
<script src="../js/offer.js"></script> <!-- Script for handling offers (if applicable) -->