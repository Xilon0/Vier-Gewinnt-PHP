<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Vier Gewinnt</title>
        <link rel="stylesheet" href="styles/background.css">
        <link rel="stylesheet" href="styles/game.css">

        <script src="https://kit.fontawesome.com/1a20e31c0a.js" crossorigin="anonymous"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <img src="images/background.png" id="background">
        <button onclick="simulateTurn(5,1);" style="position: absolute; top:0; left:0">simulateOpponantTurn</button> <!--Debug Button | REMOVE LATER-->
        <div class="wrapper">
            <div class="gameField" id="gameField"></div>
        </div>
        <div class="player-wrapper" id="player-1-wrapper">
            <div class="player-Bubble" id="User-1-Bubble">
                <h1 id="User-1-Name"></h1>
            </div>
            <i class="fa-solid fa-hand-holding" id="turnIcon-1"></i>
        </div>
        <div class="player-wrapper" id="player-2-wrapper">
            <div class="player-Bubble" id="User-2-Bubble">
                <h1 id="User-2-Name"></h1>
            </div>
            <i class="fa-solid fa-hand-holding" id="turnIcon-2"></i>
        </div>

        <script>

            let fieldArray = new Array(7).fill().map(() => new Array(6).fill(2));;
            let playerOne;
            let playerTwo;
            let player;

            initialize();
            

            function getDatabase() {

                fieldArray;
                playerOne = "Player 1";
                playerTwo = "Player 2";
                lastTurnPlayer = 1;
                player = 0;

                /*$.ajax({
                    url: 'getDatabase.php',
                    type: 'GET',
                    success: function(response) {
                        // { "gameBoard": [[...]], "statusMessage": "Player 2's turn" }

                        let data = JSON.parse(response);

                        fieldArray = data.gameBoard;
                        playerOne = data.playerOne;
                        playerTwo = data.playerTwo;
                        lastTurnPlayer = data.lastTurnPlayer;
                        if (playerOne == $_SESSION['username']) {
                            player = 0;
                        } else if (playerTwo ==  $_SESSION['username']) {
                            player = 1;
                        } else {
                            window.location.href("index.php");
                        }

                        update();

                        setTimeout(getDatabase(), 1000);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', status, error);
                        setTimeout(getDatabase(), 1000);
                    }
                });*/
            }
            
            function initialize() {

                getDatabase();

                if ( player == 1 ) {
                    const tempColor = getComputedStyle(document.body).getPropertyValue('--player-1');
                    document.documentElement.style.setProperty('--player-1', getComputedStyle(document.body).getPropertyValue('--player-2'));
                    document.documentElement.style.setProperty('--player-2', tempColor);
                }

                document.getElementById("User-1-Name").textContent = playerOne;
                document.getElementById("User-2-Name").textContent = playerTwo;

                const gameField = document.getElementById('gameField');

                for (let i = 0; i < 7; i++) {
                    const row = document.createElement('div');
                    row.className = 'row';
                    row.id = `row-${i}`;
                    row.value = i;
                    row.onclick = function() { selectRow(i); };
                    for (let j = 0; j < 6; j++) {
                        const button = document.createElement('button');
                        button.value = `${i}`;
                        button.className = 'selectable';
                        button.id = `${i}|${j}`;
                        row.appendChild(button);
                    }
                    const rowSelector = document.createElement('i');
                    rowSelector.value = `${i}`;
                    rowSelector.className = "fa-solid fa-angle-down fa-bounce rowSelector";
                    row.appendChild(rowSelector);
                    gameField.appendChild(row);
                }

                update();

            }

            function setPlayerControl(state) {
                if (state == true) {
                    document.getElementById("gameField").style.pointerEvents = "all";
                    document.getElementById("turnIcon-1").style.opacity = "1";
                    document.getElementById("turnIcon-2").style.opacity = "0.3";
                    document.getElementById("turnIcon-1").classList.add("fa-beat");
                    document.getElementById("turnIcon-2").classList.remove("fa-beat");
                } else {
                    document.getElementById("gameField").style.pointerEvents = "none";
                    document.getElementById("turnIcon-1").style.opacity = "0.3";
                    document.getElementById("turnIcon-2").style.opacity = "1";
                    document.getElementById("turnIcon-1").classList.remove("fa-beat");
                    document.getElementById("turnIcon-2").classList.add("fa-beat");
                }
            }

            function setField(i,j,p) {
                if (player == 1) {
                    p = 1 - p;
                }
                if (p == 0) {
                    let color = getComputedStyle(document.body).getPropertyValue('--player-1');
                    document.getElementById(`${i}|${j}`).style.backgroundColor = color;
                } else {
                    let color = getComputedStyle(document.body).getPropertyValue('--player-2');
                    document.getElementById(`${i}|${j}`).style.backgroundColor = color;
                }
            }

            function update() {
                for (let i = 0; i < 7; i++) {
                    for (let j = 0; j < 6; j++) {
                        if (fieldArray[i][j] != 2) {
                            setField(i,j,fieldArray[i][j]);
                        }
                    }
                }
                if (lastTurnPlayer == player) {
                    setPlayerControl(false);
                } else {
                    setPlayerControl(true);
                }
            }

            function selectRow(row) {
                fetch("push.php?row="+row);
                //pushInput to php
                simulateTurn(row,player); //debug
            }









            function simulateTurn(i,p) { //DEBUG REMOVE LATER
                //pushInput to php
                let placed = false
                for (let j = 0; j < 6; j++) {
                    if (fieldArray[i][j] != 2) {
                        fieldArray[i][j-1] = p;
                        placed = true;
                        break;
                    }
                }
                if (!placed) {
                    fieldArray[i][5] = p;
                }

                if (lastTurnPlayer == 0)
                    lastTurnPlayer = 1
                else 
                    lastTurnPlayer = 0

                update()
            }

        </script>
    </body>
</html>