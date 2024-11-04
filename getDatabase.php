<?php

include "dbconnect.php";
include "wincondition.php"; // Include the wincondition.php file for the winner function

session_start();

if (!isset($_SESSION['gameID'])) {
    echo json_encode(['error' => 'No game in progress.']);
    exit;
}

$gameID = $_SESSION['gameID'];

// Fetch the game data
$sql = "SELECT field, username1, username2, currentPlayer FROM games WHERE gameID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $gameID);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    error_log("SQL Error: " . $conn->error);
    echo json_encode(['error' => 'Database error.']);
    exit;
}

$gamedata = $result->fetch_assoc();

if ($gamedata) {
    $fieldArray = json_decode($gamedata['field'], true); // Use true to get an associative array
    $playerOne = $gamedata['username1'];
    $playerTwo = $gamedata['username2'];
    $lastTurnPlayer = $gamedata['currentPlayer'];

    // Determine the winner
    $winner = 0; // Default to no winner
    if (winner($fieldArray, 1)) {
        $winner = $playerOne; // Player 1 has won
    } elseif (winner($fieldArray, 2)) {
        $winner = $playerTwo; // Player 2 has won
    }

    // Prepare the response
    $response = [
        'gameBoard' => $fieldArray,
        'playerOne' => $playerOne,
        'playerTwo' => $playerTwo,
        'lastTurnPlayer' => $lastTurnPlayer,
        'winner' => $winner, // Add winner to the response
    ];

    // Debugging output (remove or comment out in production)
    error_log("Response: " . print_r($response, true)); // Log the response for debugging

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Redirect to exit.php if the game does not exist
    header('Location: exit.php');
    exit();
}

?>
