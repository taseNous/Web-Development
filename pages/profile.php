<?php

session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    
    header("Location: ../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <nav class="navbar">
        <?php include '../includes/navbar.php'; ?>
    </nav>

    
    <div class="profile">
        <div class="row">
            <h1>Create New Profile for Rescuer</h1>
        </div>

        <br>

        <div class="signup-rescuer">
            <form action="../php/process_signup_rescuer.php" method="post" id="Signup" novalidate>
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username">
                </div>

                <div>
                    <label for="lat">Latitude</label>
                    <input type="text" id="lat" name="lat">
                </div>

                <div>
                    <label for="lon">Longitude</label>
                    <input type="text" id="lon" name="lon">
                </div>
                
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <div>
                    <label for="password_confirmation">Repeat password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                </div>
                
                <button style="width: auto; padding: 10px 20px;">Sign up rescuer</button>

            </form>
        </div>
    </div>

    <footer class="row">
        <?php include '../includes/footer.php';?>
    </footer>

</body>
</html>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>