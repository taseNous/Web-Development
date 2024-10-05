<?php

session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["usertype"]) || $_SESSION["usertype"] == "rescuer") {
    
    header("Location: ../index.php");
    exit;
}

$mysqli = require __DIR__ . "/../php/database.php";
$user_id = $_SESSION["user_id"];
$sql = "SELECT usertype FROM user WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($usertype);
$stmt->fetch();
$stmt->close();

$_SESSION['usertype'] = $usertype;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Announcement</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <nav class="navbar">
        <?php include '../includes/navbar.php'; ?>
    </nav>
    
    <br>

    <?php if ($usertype === 'admin'): ?>

        <div class="announcement-admin">
            <br>
            <br>

            <div class="announcement-header">
                <h3>Make a new Announcement</h3>
            </div>
            <div class="announcement-form-admin">
                <form action="../php/add_to_announcement.php" method="post" id="addItemForm" novalidate>

                    <div class="form-group">
                        <label for="productCategory">Category</label>
                        <select id="productCategory" class="form-control">
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="productName">Product</label>
                        <select id="productName" class="form-control">
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Announce</button>

                </form>
            </div>
        </div>

        <div class="announcement-history-table">

                <h2>Announcements</h2>
                

                <table id="inventoryStatusAdmin" class="table_announcement">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Product</th> 
                        </tr>
                    </thead>

                    <tbody>
                        
                    </tbody>


                </table>

            </div>    

    <?php endif; ?>

    <?php if ($usertype === 'citizen'): ?>

        <div class="announcement-citizen">

            <div class="announcement-history-header">
                <h2>Announcements History</h2>
            </div>

            <br>

            <div class="announcement-form-citizen">
                <div class="form-group col-md-4">
                    <label for="categoryFilter">Filter by Category:</label>
                    <select id="categoryFilter" class="form-control">
                    <option value="all">All Categories</option>
                    </select>
                </div>
            </div>

            <div class="announcement-history-table">

                <table id="inventoryStatus" class="table_announcement">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Product</th> 
                            <th>Quantity</th>
                            <th>Action</th> 
                        </tr>
                    </thead>

                    <tbody>
                        
                        
                    </tbody>


                </table>

            </div>    
    
        </div>

        <div class="user-offers-section">
    <div class="user-offers-header">
        <h2>Your Offers</h2>
    </div>

    <br>

    <div class="user-offers-table">
        <table id="userOffersTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

    <?php endif; ?>

    <footer class="row">
        <?php include '../includes/footer.php';?>
    </footer>

</body>
</html>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/announcement1.js"></script> 
<script src="../js/fetch_offers.js"></script> 
<script src="../js/fetch.js"></script> 
<script src="../js/fetch_announcements_admin.js"></script>
<script src="../js/drop.js"></script>
<script src="../js/offer.js"></script>