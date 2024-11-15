<?php
$servername = "192.168.242.251"; // MySQL server (localhost)
$username = "dbuser";        // MySQL username (root)
$password = "dbuser";            // No password for root by default in XAMPP
$database = "vier_gewinnt"; // The database we created

// Create connection (note the new port 3307)
$conn = mysqli_connect($servername, $username, $password, $database, 3306);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>