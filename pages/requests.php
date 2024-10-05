<?php

session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "citizen") {
    
    header("Location: ../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Requests</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <nav class="navbar">
        <?php include '../includes/navbar.php'; ?>
    </nav>

    <br>
    <br>
    
    <div class="add-requests">
        <div class="make-request-header">
            <h3>Make a Request</h3>
            <br>
        </div>
        <div class="request-form">
            <form action="../php/add_request.php" method="post" id="addItemForm" novalidate>
                <div class="request-search">
                    <label for="fname">Search for a Product:</label>
                    <input type="text" id="fname" name="fname" value="Search.." class="form-control"><br>
                </div>
                <div class="dropdown-category">
                    <label for="productCategory">Category</label>
                    <select id="productCategory" class="form-control"> </select>
                </div>
                <div class="dropdown-product">
                    <label for="productName">Product</label>
                    <select id="productName" class="form-control">
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
                <div class="input-number">
                    <label for="productQuantity">Number of People</label>
                    <input type="number" id="productQuantity" class="form-control" placeholder="Enter quantity">
                </div>
                <button type="submit" class="btn btn-primary">Complete the Request</button>
            </form>
        </div>
    </div>
    
    <div class="request-history">
        <div class="requestHistory-header">
            <h2>Requests History</h2>
        </div>

        <div class="request-table">
            <table id="userdata" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Number of People</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows for requests history will be populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <footer class="row">
        <?php include '../includes/footer.php';?>
    </footer>
    
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="../js/add_requests.js"></script> 
<script src="../js/search1.js"></script>