<?php

$host = "localhost"; // The hostname of the database server.

$dbname = "web"; // The name of the database you are connecting to.

$username = "root"; // The username used to authenticate with the database.

$password = ""; // The password for the database user.


$mysqli = new mysqli(hostname: $host,     // This line creates a new instance of the MySQLi class and tries to establish a connection to the MySQL database.
                     username: $username, // The arguments are passed using named parameters, making it clearer which argument is which.
                     password: $password, 
                     database: $dbname);
                     
if ($mysqli->connect_errno) { // Checks if there was an error during the connection attempt. "connect_errno" is a property of the $mysqli object that returns the error code (if any).
    
    die("Connection error: " . $mysqli->connect_error); // If there's a connection error, the script stops execution (using `die()`) and outputs a message along with the specific connection error using `connect_error`.
}

return $mysqli; // Returns the established MySQLi connection object, allowing it to be used in other parts of your application (e.g., queries, data retrieval). This is often used when this script is included in other files.