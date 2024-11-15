<?php
    session_start();
    if (isset($_SESSION['username'])) {
        if (isset($_SESSION['gameID'])) {
            if ($_SESSION['gameID'] != null) {
                $gameID = $_SESSION['gameID'];
                header('Location: game.php?id='.$gameID);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Vier Gewinnt</title>
        <link rel="stylesheet" href="styles/background.css">
        <link rel="stylesheet" href="styles/lobby.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jaro:opsz@6..72&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
        
    </head>
    <body>
        <img src="images/background.png" id="background">

        <form id="form">
            <p id="title">VIER GEWINNT</p>
            <div style="position:relative;display:flex">
                <input type="text" id="username" name="username" placeholder="Name" required>
                <i class="fa-solid fa-face-frown" id="user-icon"></i>
            </div>
            <button type="submit">Beitreten</button>
        </form>
            
        <script>
            const inputField = document.getElementById("username");
            const userIcon = document.getElementById("user-icon");
            
            function checkInput() {
                const inputValue = inputField.value.trim();
                if (inputValue == "") {
                    userIcon.className = "fa-solid fa-face-frown";
                } else {
                    userIcon.className = "fa-solid fa-face-smile";
                }
            }

            inputField.addEventListener('input', checkInput);

            document.getElementById("form").addEventListener("submit", async function(event) {
                event.preventDefault();

                const inputValue = inputField.value.trim();
                if (inputValue == "") {
                    return;
                }
                
                const formData = new FormData(this);
                
                try {
                const response = await fetch('postForm.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.gameID != "full") {
                    joinGame(result.gameID);
                } else {
                    alert("Warte bis die laufende Sitzung beendet wurde.")
                    console.error('No gameID returned:', result);
                }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
            
            function joinGame(id) {
                document.getElementById("form").classList.add("slideOut");
                setTimeout(() => {
                    window.location.href = "game.php?id="+id;
                }, "1500");
            }
        </script>
    </body>
</html>