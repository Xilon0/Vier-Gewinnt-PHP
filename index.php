<?php
session_start();
include "dbconnect.php";

if(!isset($_SESSION["gameID"])) {
    $field = json_encode(array_fill(0, 6, array_fill(0, 7, 0)));
    $sql = "INSERT INTO games (player1, player2, field, currentPlayer) VALUES ('Timmy', 'Johannes', '$field', 1)";
    if($conn->query($sql) === TRUE){
        $_SESSION["gameID"] = $conn->insert_id;
    }
}

$gameID = $_SESSION["gameID"];
$sql = "SELECT * FROM games WHERE id=$gameID";
$result = $conn->query($sql);
$game = $result->fetch_assoc();
$field = json_decode($game['field'], true);
$currentPlayer = $game['currentPlayer'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <form method="POST" action="dbconnect.php">
        <button>Test</button>
    </form>
</body>
</html>