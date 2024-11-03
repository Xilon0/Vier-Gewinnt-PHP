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
    $gameID = sqlQuery("SELECT gameID FROM games", "gameID", $conn);

    if ($gameID === null) {
        $emptyField = json_encode(array_fill(0, 7, array_fill(0, 6, 0)));
        $sql = "INSERT INTO games (player1, player2, field, currentPlayer) VALUES ('0', '0', '$emptyField', 1)";

        if ($conn->query($sql)) {
            return $conn->insert_id;
        } else {
            error_log("SQL Error: " . $conn->error);
            return null;
        }
    }

    return $gameID;
}

$session_Id = session_id();
$gameID = initializeGame($conn);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usernameFrontend = $_POST['username'];

    $result = $conn->query("select * from games where gameID = $gameID"); 
    $gamedata = $result->fetch_assoc();

    $player1 = $gamedata['player1'];
    $player2 = $gamedata['player2'];

    if($player1 == "0") {
        $sql = "update games set player1 = '$session_Id', username1 = '$usernameFrontend' where gameID = $gameID";
        $conn->query($sql);

        $_SESSION['username'] = $usernameFrontend;
        $_SESSION['gameID'] = $gameID;
        $_SESSION['player'] = 1;
        $response = [
            'gameID' => $gameID
        ];

        header('Content-Type: application/json');
  
        echo json_encode($response);

    } else {
        
        if($player2 == "0" && $player1 != $session_Id ) {
            $sql = "update games set player2 = '$session_Id', username2 = '$usernameFrontend' where gameID = $gameID";
            
            $_SESSION['username'] = $usernameFrontend;
            $_SESSION['gameID'] = $gameID;
            $_SESSION['player'] = 2;
            $response = [
                'gameID' => $gameID
            ];

            header('Content-Type: application/json');
    
            echo json_encode($response);
            
            if (!$conn->query($sql)) {
                error_log("SQL Error: " . $conn->error);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Database error']);
                exit;
            }
        } else if($player1 == $session_Id OR $player2 == $session_Id) { 
           
            $_SESSION['username'] = $usernameFrontend;
            $_SESSION['gameID'] = $gameID;
            $response = [
                'gameID' => $gameID
            ];

            header('Content-Type: application/json');
     
            echo json_encode($response);
        
            
        } else {

            $response = [
                'gameID' => 'full',
                'gamedata' => $gamedata,
                'playerName' => $usernameFrontend,
                'sessionID' => $session_Id
            ];
        
            header('Content-Type: application/json');
        
            echo json_encode($response);
        }

    }
}
