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
        <div class="wrapper">
            <div class="gameField" id="gameField"></div>
        </div>
        <div class="player-wrapper" id="player-1-wrapper">
            <div class="player-Bubble" id="User-1-Bubble">
                <h1 id="User-1-Name"></h1>
            </div>
            <i class="fa-solid fa-hand-holding" id="turnIcon-1"></i>
            <i class="fa-solid fa-arrow-right-from-bracket" id="exitIcon" onclick="exitRound()"></i>
        </div>
        <div class="player-wrapper" id="player-2-wrapper">
            <div class="player-Bubble" id="User-2-Bubble">
                <h1 id="User-2-Name"></h1>
            </div>
            <i class="fa-solid fa-hand-holding" id="turnIcon-2"></i>
        </div>

        <script>

            let fieldArray;
            let playerOne;
            let playerTwo;
            let player;
            let lastTurnPlayer;
            let winnner = "none";

            initialize();
            

            function getDatabase() {
                fieldArray;
                playerOne = "Player 1";
                playerTwo = "Player 2";
                lastTurnPlayer = 1;
                player = 0;
                let usernameFromSession = "<?php echo $_SESSION['username'] ?>";

                $.ajax({
                    url: 'getDatabase.php',
                    type: 'GET',
                    dataType: 'json', // Specify that you're expecting JSON
                    success: function(data) { // Directly use the parsed JSON data
                        fieldArray = data.gameBoard;
                        playerOne = data.playerOne;
                        playerTwo = data.playerTwo;
                        lastTurnPlayer = data.lastTurnPlayer;
                        winner = data.winner;
                        
                        if (usernameFromSession == playerOne) {
                            document.getElementById("User-1-Name").textContent = playerOne;
                            document.getElementById("User-2-Name").textContent = playerTwo;
                        } else {
                            document.getElementById("User-2-Name").textContent = playerOne;
                            document.getElementById("User-1-Name").textContent = playerTwo;  
                        }

                        // Assuming $_SESSION['username'] is accessible via some JS variable
                        if (playerOne === usernameFromSession) { // Use the actual variable holding session username
                            player = 0;
                        } else if (playerTwo === usernameFromSession) {
                            player = 1;
                        } else {
                            window.location.href = "index.php"; // Correct way to redirect
                        }

                        if ( player == 1 ) {
                            document.documentElement.style.setProperty('--player-1', getComputedStyle(document.body).getPropertyValue('--color-2'));
                            document.documentElement.style.setProperty('--player-2', getComputedStyle(document.body).getPropertyValue('--color-1'));
                        }

                        update();

                        setTimeout(getDatabase, 2000); // Call the function, do not invoke it
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', status, error);
                        setTimeout(getDatabase, 2000); // Call the function, do not invoke it
                    }
                });
            }
            
            function initialize() {

                getDatabase();

                const gameField = document.getElementById('gameField');

                for (let i = 0; i < 7; i++) {
                    const row = document.createElement('div');
                    row.className = 'row';
                    row.id = `row-${i}`;
                    row.value = i;
                    row.onclick = function() { selectColumn(i); };
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
                if (p == 1) {
                    let color = getComputedStyle(document.body).getPropertyValue('--color-1');
                    document.getElementById(`${i}|${j}`).style.backgroundColor = color;
                } else if (p == 2) {
                    let color = getComputedStyle(document.body).getPropertyValue('--color-2');
                    document.getElementById(`${i}|${j}`).style.backgroundColor = color;
                }
            }

            function update() {
                for (let i = 0; i < 7; i++) {
                    for (let j = 0; j < 6; j++) {
                        if (fieldArray)
                            setField(i,j,fieldArray[j][i]);
                    }
                }
                if (lastTurnPlayer == player) {
                    setPlayerControl(false);
                } else {
                    setPlayerControl(true);
                }
            }

            function selectColumn(column) {
                simulateColumnPlacement(column);
                fetch("push.php?column=" + column);
            }

            function simulateColumnPlacement(i) {
                let placed = false
                for (let j = 5; j >= 0; j--) {
                    if (fieldArray[j][i] == 0 || fieldArray[j][i] == null) {
                        setField(i,j,player+1);
                        lastTurnPlayer = 1 - lastTurnPlayer;
                        placed = true;
                        break;
                    }
                }
                update()
            }

            function exitRound() {
                window.location.href = "exit.php"
            }

        </script>
    </body>
</html>