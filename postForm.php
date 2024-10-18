<?php

include "dbconnect.php";

//game id muss in sql gesetzt, ist der primärschlussel
//und db wird nach jedem game gelöscht

// cleart die datenbank; NUR ZUM TESTEN DANACH MUSS RAUS
//$sql = "delete from games";
//$conn->query($sql); 

// prüfen ob es bereits ein game gibt oder eins erstellt werden muss


function sqlQuery($statement, $str, $conn) {
    $sql = $statement;
    $result = $conn->query($sql); 
    $row = $result->fetch_assoc(); // fetch_assoc konvertiert es zu einem array 
    if($row != null) {
        return $row[$str];
    } else {
        $field = json_encode(array_fill(0, 6, array_fill(0, 7, 0)));
        $sql = "INSERT INTO games (player1, player2, field, currentPlayer) VALUES ('0', '0', '$field', 1)";
        $conn->query($sql); 
    }
}

sqlQuery("select * from games", "gameID", $conn);


session_start();
$session_Id = session_id();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the value from the POST request -> Wenn Noel auf Start Game drückt wird ein Post
    // Request gesendet mit dem Username
    $usernameFrontend = $_POST['username'];

    // gameid = primary key von der datenbank
    $gameID = sqlQuery("select * from games", "gameID", $conn);

    // BENUTZERNAME wird PER GET REQUEST ÜBERGEBEN und dann in die db geschrieben
    // rückgabe der gameid wenn ein spieler definiert ist, wenn volll dann false zurückgeben

    $playerdata = sqlQuery("select player1 from games where gameID = $gameID", "player1", $conn);

    // if player1 == 0 -> spieler 1 wurde noch nicht gesetzt
    if($playerdata == "0") {
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
        $result = $conn->query("select player2 from games where gameID = $gameID"); 
        $row = $result->fetch_assoc();
        $playerdata = $row['player2'];
        if($playerdata == "0") {
        $sql = "update games set player2 = $session_Id, username2 = $usernameFrontend where gameID = $gameID";
        if (!$conn->query($sql)) {
            // Log the error and return a JSON error response
            error_log("SQL Error: " . $conn->error);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error']);
            exit;
        }
        } else { // falls dieser nicht frei ist, ist das game voll.
           
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
