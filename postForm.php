<?php

include "dbconnect.php";

session_start();

//game id muss in sql gesetzt, ist der primärschlussel
//und db wird nach jedem game gelöscht

// cleart die datenbank; NUR ZUM TESTEN DANACH MUSS RAUS
//$sql = "delete from games";
//$conn->query($sql); 

// prüfen ob es bereits ein game gibt oder eins erstellt werden muss

function sqlQuery($sql, $column, $conn) {
    $result = $conn->query($sql);
    
    if ($result === false) {
        error_log("SQL Error: " . $conn->error);
        return null;
    }

    $row = $result->fetch_assoc();
    
    if ($row !== null) {
        return $row[$column];
    } else {
        return null;
    }
}

function initializeGame($conn) {
    // Check if there's an existing game
    $gameID = sqlQuery("SELECT gameID FROM games", "gameID", $conn);

    // If no game exists, create a new one
    if ($gameID === null) {
        $emptyField = json_encode(array_fill(0, 6, array_fill(0, 7, 0))); // 6x7 grid
        $sql = "INSERT INTO games (player1, player2, field, currentPlayer) VALUES ('0', '0', '$emptyField', 1)";

        if ($conn->query($sql)) {
            return $conn->insert_id; // Return the newly created game ID
        } else {
            error_log("SQL Error: " . $conn->error);
            return null;
        }
    }

    // Return existing game ID
    return $gameID;
}

$session_Id = session_id();
$gameID = initializeGame($conn);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the value from the POST request -> Wenn Noel auf Start Game drückt wird ein Post
    // Request gesendet mit dem Username
    $usernameFrontend = $_POST['username'];

    // BENUTZERNAME wird PER GET REQUEST ÜBERGEBEN und dann in die db geschrieben
    // rückgabe der gameid wenn ein spieler definiert ist, wenn volll dann false zurückgeben

    $result = $conn->query("select * from games where gameID = $gameID"); 
    $gamedata = $result->fetch_assoc();

    $player1 = $gamedata['player1'];
    $player2 = $gamedata['player2'];

    // if player1 == 0 -> spieler 1 wurde noch nicht gesetzt
    if($player1 == "0") {
        $sql = "update games set player1 = '$session_Id', username1 = '$usernameFrontend' where gameID = $gameID";
        $conn->query($sql);

        $response = [
            'gameID' => $gameID
        ];
    
        // Set the content type to application/json
        header('Content-Type: application/json');
    
        // Send the JSON response back to the frontend
        echo json_encode($response);

    } else { // sonst wird in db player2 geschaut ob dieser frei ist 
        
        if($player2 == "0" && $player1 != $session_Id ) {
        $sql = "update games set player2 = '$session_Id', username2 = '$usernameFrontend' where gameID = $gameID";
        
        $response = [
            'gameID' => $gameID
        ];
        
        // Set the content type to application/json
        header('Content-Type: application/json');
    
        // Send the JSON response back to the frontend
        echo json_encode($response);
        
        if (!$conn->query($sql)) {
            // Log the error and return a JSON error response
            error_log("SQL Error: " . $conn->error);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error']);
            exit;
        }
        } else if($player1 == $session_Id OR $player2 == $session_Id) { 
           
            $response = [
                'gameID' => $gameID
            ];
        
            // Set the content type to application/json
            header('Content-Type: application/json');
        
            // Send the JSON response back to the frontend
            echo json_encode($response);
        
            
        } else {
            $response = [
                'gameID' => 'full'
            ];
        
            // Set the content type to application/json
            header('Content-Type: application/json');
        
            // Send the JSON response back to the frontend
            echo json_encode($response);
        }

    }
}
