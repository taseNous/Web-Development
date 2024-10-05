<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";

    // Prepare and execute the query securely
    $sql = sprintf("SELECT * FROM user WHERE username = '%s'", 
                   $mysqli->real_escape_string($_POST["username"]));
    
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
    
    if ($user) {
        
        // Verify the password
        if (password_verify($_POST["password"], $user["password_hash"])) {
            
            session_start();
            
            session_regenerate_id();
            
            // Store user ID and usertype in the session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["usertype"] = $user["usertype"];

            // Redirect based on usertype
            if ($user["usertype"] === "citizen") {
                header("Location: ../index.php");
            } elseif ($user["usertype"] === "admin") {
                header("Location: ../index.php");
            } elseif ($user["usertype"] === "rescuer") {
                header("Location: ../index.php");
            }
            
            exit;
        }
    }
    
    $is_invalid = true;
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    
    <link rel="stylesheet" href="Style.css">

    <style>

        .container-fluid-login {

            background-color: #343a40; /* Red background for the rectangle */
            padding: 20px 40px; /* Padding to create space inside the rectangle */
            border-radius: 8px; /* Optional: Rounded corners for a softer rectangle */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Subtle shadow for depth */
            color: white; /* Ensure text color contrasts with the red background */
        }


    </style>
</head>
<body>

    <div class="container-fluid-login">
        
        <!--<div class="row">
            <div class="col">  </div>
        </div> -->

        <div class="row">
            <h1>Login</h1>
            
            <?php if ($is_invalid): ?>
                <em>Invalid login</em>
            <?php endif; ?>
            
            <form method="post">
                <label for="username">Username</label>
                <input type="text" name="username" id="username"
                    value="<?= htmlspecialchars($_POST["username"] ?? "") ?>">
                <br>
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
                <br>
                <button>Log in</button>
            </form>
        </div>
    <!--
        <div class="row">

             

        </div> -->
    
    
</body>
</html>