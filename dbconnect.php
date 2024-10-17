<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "vier_gewinnt";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

header("Location: index.php");
?>