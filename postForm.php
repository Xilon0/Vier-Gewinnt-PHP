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
$session_id = session_id();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the value from the POST request -> Wenn Noel auf Start Game drückt wird ein Post
    // Request gesendet mit dem Username
    $usernameFrontend = $_POST['username'];

    // gameid = primary key von der datenbank
    $gameid = sqlQuery("select * from games", "gameid", $conn);

    // BENUTZERNAME wird PER GET REQUEST ÜBERGEBEN und dann in die db geschrieben
    // rückgabe der gameid wenn ein spieler definiert ist, wenn volll dann false zurückgeben

    $playerdata = sqlQuery("select player1 from games where gameID = $gameid", "player1", $conn);

    // if player1 == 0 -> spieler 1 wurde noch nicht gesetzt
    if($playerdata == "0") {
        $sql = "update games set player1 = $sessionId, set username1 = $usernameFrontend where gameID = $gameid";
        $conn->query($sql);

        $response = [
            'message' => $gameid
        ];
    
        // Set the content type to application/json
        header('Content-Type: application/json');
    
        // Send the JSON response back to the frontend
        echo json_encode($response);

    } else { // sonst wird in db player2 geschaut ob dieser frei ist 
        $result = $conn->query("select player2 from games where gameID = $gameid"); 
        $row = $result->fetch_assoc();
        $playerdata = $row['player2'];
        if($playerdata == "0") {
        $sql = "update games set player2 = $sessionId, username2 = $usernameFrontend where gameID = $gameid";
        $conn->query($sql);
        } else { // falls dieser nicht frei ist, ist das game voll.
           
            $response = [
                'message' => 'full'
            ];
        
            // Set the content type to application/json
            header('Content-Type: application/json');
        
            // Send the JSON response back to the frontend
            echo json_encode($response);
        
            
        }

    }
}
