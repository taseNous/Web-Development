<?php
session_start(); // Starts the session, which allows tracking user information across pages, if it already exists (the user is logged in), it resumes the session. 

if (isset($_SESSION["user_id"])) { // Checks if a user is logged in by looking for the session variable "user_id" in the session. If this is set, it means the user is logged in.

    $mysqli = require __DIR__ . "/php/database.php"; // Connects to the database. "Database.php" contains the connection logic, typically using MySQLi. The "__DIR__" gets the directory of the current file.

    $sql = "SELECT * FROM user WHERE id = {$_SESSION["user_id"]}"; // Creates an SQL query to fetch the details of the user currently logged in. The user's ID is fetched from the session and dynamically inserted into the query.

    $result = $mysqli->query($sql); // Runs the SQL query against the database, returning the result object.

    $user = $result->fetch_assoc(); // Fetches the result row as an associative array, meaning data can be accessed by column names like $user['username']. If no user is found, this will return null.
    
}
?>

<!DOCTYPE html>

<html lang="en"> <!-- Declares the document as HTML5 and sets the language to English. -->
<head>
    <meta charset="UTF-8"> <!-- Specifies the character encoding as UTF-8, which supports a wide range of characters. -->
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Ensures the webpage is responsive by controlling how the page is scaled on different devices. -->

    <title>Homepage</title>  <!-- The title of the webpage as shown in the browser's title bar or tab. -->

    <link rel="stylesheet" href="css/style9.css"> <!-- Links to an external CSS file that styles the page. -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0"> <!-- Links to a Google Fonts stylesheet for Material Symbols Rounded, used for icons. -->
</head>

<body>
    <header>
        <?php include 'includes/navbar.php'; ?>  <!-- Includes the contents of "navbar.php" here, which likely contains the website's navigation bar. -->
    </header>

    <div class="blur-bg-overlay"></div> <!-- A background overlay (styled in CSS) to blur the background when a form pops up. -->

    <!-- Greet the user after login -->
    <div class="greeting">
        <?php if (isset($user)): ?>
            <h1>Hello, <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['usertype']); ?>)</h1>
        <?php else: ?>
            <h1>Welcome to our site!</h1>
        <?php endif; ?>
    </div>

    <div class="form-popup">
        <span class="close-btn material-symbols-rounded">close</span>  <!-- A close button for the popup window, styled using Material Symbols. -->
        
        <div class="form-box login"> <!-- Contains the login form. -->

            <div class="form-details">
                <h2>Welcome Back</h2>
                <p>Please log in using your personal information to stay connected</p>
            </div>

            <div class="form-content">
                <h2>Login</h2>
                <form action="php/login.php" method="post"> <!-- The form posts data to "login.php" when the user submits it. -->
                
                    <div class="input-field">
                        <input type="text" name="username"> <!-- Text input field for the username. -->
                        <label>Username</label> 
                    </div>

                    <div class="input-field">
                        <input type="password" name="password"> 
                        <label>Password</label> 
                    </div>

                    <button type="submit">Log In</button>
                </form>

                <div class="bottom-link">
                    Don't have an account?
                    <a href="#" id="signup-link">Signup</a> <!-- A link to switch to the signup form (script.js). -->
                </div>
            </div>
        </div>

        <div class="form-box signup">
            
            <div class="form-details">
                <h2>Create Account</h2>
                <p>To become a part of our community, please sign up using your personal information</p>
            </div>

            <div class="form-content">
                <h2>Signup</h2>
                <form action="php/process-signup.php" method="post" id="signup"> <!-- Posts the form data to "process-signup.php" to handle signup logic. -->

                    <div class="input-field">
                        <input type="text" id="username" name="username">
                        <label>Username</label>
                    </div>
                    <div class="input-field">
                        <input type="text" id="first_name" name="first_name">
                        <label>First Name</label>
                    </div>
                    <div class="input-field">
                        <input type="text" id="last_name" name="last_name">
                        <label>Last Name</label>
                    </div>
                    <div class="input-field">
                        <input type="text" id="phone" name="phone">
                        <label>Phone</label>
                    </div>
                    <div class="input-field">
                        <input type="text" id="latitude" name="latitude">
                        <label>Latitude</label>
                    </div>
                    <div class="input-field">
                        <input type="text" id="longitude" name="longitude">
                        <label>Longitude</label>
                    </div>
                    <div class="input-field">
                        <input type="password" id="password" name="password">
                        <label>Password</label>
                    </div>
                    <div class="input-field">
                        <input type="password" id="password_confirmation" name="password_confirmation">
                        <label>Repeat Password</label>
                    </div>

                    <div class="policy-text">
                        <input type="checkbox" name="policy">
                        <label for="policy">
                            I agree the
                            <a href="https://tenor.com/el/search/happy-dog-meme-gifs">Terms & Conditions</a>
                        </label>
                    </div>
                    <button type="submit">Signup</button> 
                </form>

                <div class="bottom-link">
                    Already have an account?
                    <a href="#" id="login-link">Login</a> <!-- A link to switch to the login form (script.js). -->
                </div>
            </div>
        </div>
    </div>

    <footer>
        <?php include 'includes/footer.php'; ?> 
    </footer>
</body>

</html>

<script src="js/script.js" defer></script> <!-- Links to an external JavaScript file. "defer" ensures the script loads after the HTML is parsed, improving page load performance. -->
<script src="js/validation.js" defer></script>