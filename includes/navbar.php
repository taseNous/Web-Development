<header>
    <nav class="navbar"> <!-- The main navigation bar.-->

        <a href="#" class="logo">
            <img src="/Web/img/logo.jpg" alt="logo"> <!-- Displays the logo. "alt" provides alternative text. -->
            <h2>Tasos Venos-Web</h2> <!-- This heading displays the name next to the logo. -->
        </a>

        <ul class="links"> <!-- Unordered list of navigation links. -->
            <li><a href="/Web/index.php">Home</a></li> 
            <li><a href="/Web/pages/map.php">Map</a></li> 
            <li><a href="/Web/pages/inventory.php">Inventory</a></li> 
            <li><a href="/Web/pages/profile.php">Profile</a></li> 
            <li><a href="/Web/pages/requests.php">Requests</a></li> 
            <li><a href="/Web/pages/announcement.php">Announcement</a></li> 
            <li><a href="/Web/pages/charts.php">Charts</a></li> 
        </ul>

        <?php if (isset($_SESSION["user_id"])): ?> <!-- Checks if the user is logged in by verifying if "user_id" exists in the session. -->
            <form action="/Web/php/logout.php" method="post"> <!-- If the user is logged in, show a form with a "Log Out" button. The form submits a POST request to "logout.php", which will handle logging the user out. -->
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        <?php else: ?>
            <button class="login-btn">Log In</button> <!-- If the user is not logged in, display a "Log In" button instead. This button might trigger a login popup via JavaScript or redirect the user to a login page. -->
        <?php endif; ?>

    </nav>
</header>