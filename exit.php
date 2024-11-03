<?php
session_start();
include "dbconnect.php"; // Ensure this file contains your database connection logic

if (isset($_SESSION['gameID'])) {
    $gameID = $_SESSION['gameID'];

    // Check if the game exists
    $checkSql = "SELECT COUNT(*) FROM games WHERE gameID = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $gameID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    // If the game exists, delete it
    if ($count > 0) {
        $deleteSql = "DELETE FROM games WHERE gameID = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $gameID);

        if ($deleteStmt->execute()) {
            // Optionally clear the session data related to the game
            unset($_SESSION['gameID']);
        } else {
            echo "Error deleting record: " . $conn->error;
        }

        $deleteStmt->close();
    }
}

session_destroy();
header('Location: index.php');
exit();
?>
