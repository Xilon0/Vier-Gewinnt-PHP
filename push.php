<?php

session_start();
include "dbconnect.php";

// Check if the player is set in the session
if (!isset($_SESSION['player']) || !isset($_SESSION['gameID'])) {
    echo json_encode(['error' => 'Session expired or invalid.']);
    exit;
}

$player = $_SESSION['player'];
$gameID = $_SESSION['gameID'];
$column = isset($_GET['column']) ? intval($_GET['column']) : -1; // Changed to column

// Validate the column value
if ($column < 0 || $column >= 7) { // 7 columns in the grid
    echo json_encode(['error' => 'Invalid column number.']);
    exit;
}

// Fetch the current game state
$sql = "SELECT field, currentPlayer FROM games WHERE gameID = $gameID";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['error' => 'Database error.']);
    exit;
}

$gamedata = $result->fetch_assoc();
$field = json_decode($gamedata['field'], true);
$currentPlayer = $gamedata['currentPlayer'];

// Check if the current player matches the session player
if ($currentPlayer == ($player - 1)) { // Assuming players are 0 and 1
    echo json_encode(['error' => 'It is not your turn.']);
    exit;
}

// Check if the column is full
if ($field[0][$column] != 0) { // 0 means the space is empty
    echo json_encode(['error' => 'Column is full.']);
    exit;
}

// Find the lowest empty row in the selected column
for ($i = 5; $i >= 0; $i--) { // Start from the bottom
    if ($field[$i][$column] == 0) {
        $field[$i][$column] = $player; // Set the player's value
        break;
    }
}

// Update the field in the database
$newField = json_encode($field);
$newCurrentPlayer = $currentPlayer == 1 ? 0 : 1; // Switch current player between 0 and 1
$sql = "UPDATE games SET field = '$newField', currentPlayer = $newCurrentPlayer WHERE gameID = $gameID";

if ($conn->query($sql) === false) {
    echo json_encode(['error' => 'Database update error.']);
    exit;
}

// Return success response
echo json_encode(['success' => true, 'newField' => $field]);

?>
