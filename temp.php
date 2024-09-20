<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        #timer {
            font-size: 2rem;
        }
        button {
            font-size: 1.2rem;
            margin: 10px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

    <h1>Temps écoulé</h1>
    <div id="timer">00:00:00</div>
    <button id="start">Démarrer</button>
    <button id="stop">Arrêter</button>
    <button id="reset">Réinitialiser</button>

    <script>
        let startTime;
        let timerInterval;
        let elapsedTime = 0; // Temps écoulé en millisecondes

        // Fonction pour formater le temps en hh:mm:ss
        function formatTime(timeInMs) {
            let totalSeconds = Math.floor(timeInMs / 1000);
            let hours = Math.floor(totalSeconds / 3600);
            let minutes = Math.floor((totalSeconds % 3600) / 60);
            let seconds = totalSeconds % 60;

            return (
                (hours < 10 ? "0" + hours : hours) + ":" +
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds)
            );
        }

        // Fonction pour démarrer le timer
        function startTimer() {
            startTime = Date.now() - elapsedTime;
            timerInterval = setInterval(() => {
                elapsedTime = Date.now() - startTime;
                document.getElementById("timer").textContent = formatTime(elapsedTime);
            }, 1000);
        }

        // Fonction pour arrêter le timer
        function stopTimer() {
            clearInterval(timerInterval);
        }

        // Fonction pour réinitialiser le timer
        function resetTimer() {
            clearInterval(timerInterval);
            elapsedTime = 0;
            document.getElementById("timer").textContent = "00:00:00";
        }

        // Gestion des événements
        document.getElementById("start").addEventListener("click", startTimer);
        document.getElementById("stop").addEventListener("click", stopTimer);
        document.getElementById("reset").addEventListener("click", resetTimer);
    </script>

</body>
</html>
