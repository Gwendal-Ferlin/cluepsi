<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cluepsi_bdd";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer l'ID de la partie depuis l'URL
$id_partie = isset($_GET['partie']) ? (int)$_GET['partie'] : 0;
$nb_joueurs = 0; // Initialisation

if ($id_partie > 0) {
    // Requête pour obtenir le nombre de joueurs dans la partie
    $sql_nb_joueurs = "SELECT COUNT(*) as nb_joueurs FROM joueur WHERE id_partie = ?";
    $stmt_nb_joueurs = $conn->prepare($sql_nb_joueurs);
    $stmt_nb_joueurs->bind_param("i", $id_partie);
    $stmt_nb_joueurs->execute();
    $result_nb_joueurs = $stmt_nb_joueurs->get_result();

    if ($result_nb_joueurs->num_rows > 0) {
        $row_joueurs = $result_nb_joueurs->fetch_assoc();
        $nb_joueurs = $row_joueurs['nb_joueurs']; // Nombre de joueurs dans la partie
    }

    $stmt_nb_joueurs->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>CluEpsi | Tableau</title>
    <style>
        html {
            background-color: #decdb5;
        }

        body {
            margin: 0px;
            padding: 0;
        }

        h1 {
            position: absolute;
            margin: 0;
            top: 10px;
            left: 30px;
        }

        #game-message {
            font-family: Calibri, sans-serif;
            position: fixed;
            /* Positionne par rapport à la fenêtre du navigateur */
            top: 150px;
            left: 75px;
            font-size: 2em;
            color: #ada50f;
        }

        .btn-yellow {
            width: 150px;
            height: 50px;
            background-color: #f4ec3f;
            /* Couleur de fond du bouton */
            color: #000;
            /* Couleur du texte du bouton */
            border: none;
            /* Supprimer la bordure par défaut */
            padding: 10px 20px;
            /* Espacement interne du bouton */
            font-size: 1.5em;
            /* Taille du texte */
            border-radius: 5px;
            /* Coins arrondis */
            cursor: pointer;
            /* Changer le curseur au survol */
            transition: background-color 0.3s;
            /* Effet de transition pour le survol */
            font-family: Calibri, sans-serif;
            position: fixed;
            /* Positionne par rapport à la fenêtre du navigateur */
            left: 150px;
            top: 500px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            /* Ombre du bouton */
        }

        .btn-yellow:hover {
            background-color: #e2d23f;
            /* Couleur de fond au survol */
        }

        .container {
            position: relative;
            width: 600px;
            height: 620px;
            margin: 0;
            margin: 50px;
            padding: 0;
            box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3);
            /* Ombre du bouton */
        }



        .container img {
            width: 100%;
            height: auto;
            margin: 0;
            padding: 0;
        }

        .highlight {
            background-color: rgba(200, 200, 200, 0.5) !important;
        }

        /* Pions de joueurs */
        .pion {
            position: absolute;
            width: 20px;
            height: 20px;
            z-index: 100;
        }

        #pion-1 {
            background-image: url('pion/pion1.png');
            background-size: cover;
        }

        #pion-2 {
            background-image: url('pion/pion2.png');
            background-size: cover;
        }

        #pion-3 {
            background-image: url('pion/pion3.png');
            background-size: cover;
        }

        #pion-4 {
            background-image: url('pion/pion4.png');
            background-size: cover;
        }

        #pion-5 {
            background-image: url('pion/pion5.png');
            background-size: cover;
        }

        #pion-6 {
            background-image: url('pion/pion6.png');
            background-size: cover;
        }

        .clickable-square {
            position: absolute;
            background-color: rgba(255, 255, 255, 0);
            border: 1px solid transparent;
        }

        .square {
            width: 20px;
            height: 19px;
        }

        .clickable-square:hover {
            background-color: #4CBAE2;
        }

        .clickable-square:hover.door {
            background-color: red;
        }

        /* Css de lancement de dé */

        #dice-container {
            display: flex;
            justify-content: center;
            position: fixed;
            bottom: 20px;
            /* Ajuste cette valeur pour modifier l'écart depuis le bas */
            right: 50px;
            /* Ajuste cette valeur pour modifier l'écart depuis la droite */
            z-index: 1000;
            /* Assure que l'élément est visible au-dessus des autres */
        }

        .dice {
            font-size: 3rem;
            margin: 20px;
            height: 60px;
            width: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            cursor: pointer;
        }

        button {
            padding: 10px 20px;
            font-size: 1.2rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #next-turn-button:disabled {
            opacity: 0.5;
            /* Rend le bouton plus transparent */
            cursor: not-allowed;
            /* Change le curseur pour indiquer que le bouton est désactivé */
            background-color: #ccc;
            /* Change la couleur de fond pour montrer que le bouton est désactivé */
            color: #666;
            /* Change la couleur du texte */
        }

        .rolling {
            animation: roll 0.5s infinite;
        }

        @keyframes roll {
            0% {
                transform: rotate(0deg);
            }

            10% {
                transform: rotate(30deg);
            }

            20% {
                transform: rotate(60deg);
            }

            30% {
                transform: rotate(90deg);
            }

            40% {
                transform: rotate(120deg);
            }

            50% {
                transform: rotate(150deg);
            }

            60% {
                transform: rotate(180deg);
            }

            70% {
                transform: rotate(210deg);
            }

            80% {
                transform: rotate(240deg);
            }

            90% {
                transform: rotate(270deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #timer{
            position: fixed;
            right: 30px;
            top: 20px;
            font-size: 2em;
        }
    </style>
</head>

<body>
    <h1><img src="logo.png" alt="Cluedo Title" width="40%"></h1>
    <center>
        <div class="container">
            <img src="image.png" alt="Clickable Grid" border=1>
            <?php
            for ($i = 1; $i <= $nb_joueurs; $i++) {
                echo '<div id="pion-' . $i . '" class="pion"></div>';
            }
            ?>

            <!-- Create clickable squares -->
            <a href="#" class="clickable-square square square-1-12"></a>
            <a href="#" class="clickable-square square square-1-13"></a>
            <a href="#" class="clickable-square square square-1-14"></a>
            <a href="#" class="clickable-square square square-1-15"></a>
            <a href="#" class="clickable-square square square-1-16"></a>

            <a href="#" class="clickable-square square square-2-12 door"></a>
            <a href="#" class="clickable-square square square-2-13"></a>
            <a href="#" class="clickable-square square square-2-14"></a>
            <a href="#" class="clickable-square square square-2-15"></a>
            <a href="#" class="clickable-square square square-2-16"></a>

            <a href="#" class="clickable-square square square-3-12"></a>
            <a href="#" class="clickable-square square square-3-13"></a>
            <a href="#" class="clickable-square square square-3-14"></a>
            <a href="#" class="clickable-square square square-3-15"></a>
            <a href="#" class="clickable-square square square-3-16"></a>

            <a href="#" class="clickable-square square square-4-12"></a>
            <a href="#" class="clickable-square square square-4-13"></a>
            <a href="#" class="clickable-square square square-4-14"></a>
            <a href="#" class="clickable-square square square-4-15"></a>
            <a href="#" class="clickable-square square square-4-16"></a>

            <a href="#" class="clickable-square square square-5-12"></a>
            <a href="#" class="clickable-square square square-5-13"></a>
            <a href="#" class="clickable-square square square-5-14"></a>
            <a href="#" class="clickable-square square square-5-15"></a>
            <a href="#" class="clickable-square square square-5-16 door"></a>

            <a href="#" class="clickable-square square square-6-12"></a>
            <a href="#" class="clickable-square square square-6-13"></a>
            <a href="#" class="clickable-square square square-6-14"></a>
            <a href="#" class="clickable-square square square-6-15"></a>
            <a href="#" class="clickable-square square square-6-16"></a>

            <a href="#" class="clickable-square square square-7-12"></a>
            <a href="#" class="clickable-square square square-7-13"></a>
            <a href="#" class="clickable-square square square-7-14"></a>
            <a href="#" class="clickable-square square square-7-15"></a>
            <a href="#" class="clickable-square square square-7-16"></a>

            <a href="#" class="clickable-square square square-8-12"></a>
            <a href="#" class="clickable-square square square-8-13"></a>
            <a href="#" class="clickable-square square square-8-14"></a>
            <a href="#" class="clickable-square square square-8-15"></a>
            <a href="#" class="clickable-square square square-8-16"></a>

            <a href="#" class="clickable-square square square-9-12 door"></a>
            <a href="#" class="clickable-square square square-9-13"></a>
            <a href="#" class="clickable-square square square-9-14"></a>
            <a href="#" class="clickable-square square square-9-15"></a>
            <a href="#" class="clickable-square square square-9-16"></a>
            <a href="#" class="clickable-square square square-9-17"></a>

            <a href="#" class="clickable-square square square-10-12"></a>
            <a href="#" class="clickable-square square square-10-13"></a>
            <a href="#" class="clickable-square square square-10-14"></a>
            <a href="#" class="clickable-square square square-10-15"></a>
            <a href="#" class="clickable-square square square-10-16"></a>
            <a href="#" class="clickable-square square square-10-17"></a>

            <a href="#" class="clickable-square square square-11-12"></a>
            <a href="#" class="clickable-square square square-11-13"></a>
            <a href="#" class="clickable-square square square-11-14"></a>
            <a href="#" class="clickable-square square square-11-15"></a>
            <a href="#" class="clickable-square square square-11-16"></a>
            <a href="#" class="clickable-square square square-11-17 door"></a>

            <a href="#" class="clickable-square square square-12-12"></a>
            <a href="#" class="clickable-square square square-12-13"></a>
            <a href="#" class="clickable-square square square-12-14"></a>
            <a href="#" class="clickable-square square square-12-15"></a>
            <a href="#" class="clickable-square square square-12-16"></a>
            <a href="#" class="clickable-square square square-12-17"></a>

            <a href="#" class="clickable-square square square-13-12"></a>
            <a href="#" class="clickable-square square square-13-13"></a>
            <a href="#" class="clickable-square square square-13-14"></a>
            <a href="#" class="clickable-square square square-13-15"></a>
            <a href="#" class="clickable-square square square-13-16"></a>
            <a href="#" class="clickable-square square square-13-17"></a>
            <a href="#" class="clickable-square square square-13-18"></a>
            <a href="#" class="clickable-square square square-13-19"></a>

            <a href="#" class="clickable-square square square-14-12 door"></a>
            <a href="#" class="clickable-square square square-14-13"></a>
            <a href="#" class="clickable-square square square-14-14"></a>
            <a href="#" class="clickable-square square square-14-15"></a>
            <a href="#" class="clickable-square square square-14-16"></a>
            <a href="#" class="clickable-square square square-14-17"></a>
            <a href="#" class="clickable-square square square-14-18"></a>
            <a href="#" class="clickable-square square square-14-19"></a>

            <a href="#" class="clickable-square square square-15-12"></a>
            <a href="#" class="clickable-square square square-15-13"></a>
            <a href="#" class="clickable-square square square-15-14"></a>
            <a href="#" class="clickable-square square square-15-15"></a>
            <a href="#" class="clickable-square square square-15-16"></a>
            <a href="#" class="clickable-square square square-15-17"></a>
            <a href="#" class="clickable-square square square-15-18"></a>
            <a href="#" class="clickable-square square square-15-19"></a>

            <a href="#" class="clickable-square square square-16-12"></a>
            <a href="#" class="clickable-square square square-16-13"></a>
            <a href="#" class="clickable-square square square-16-14"></a>
            <a href="#" class="clickable-square square square-16-15"></a>
            <a href="#" class="clickable-square square square-16-16"></a>
            <a href="#" class="clickable-square square square-16-17"></a>
            <a href="#" class="clickable-square square square-16-18"></a>
            <a href="#" class="clickable-square square square-16-19"></a>

            <a href="#" class="clickable-square square square-17-12"></a>
            <a href="#" class="clickable-square square square-17-13"></a>
            <a href="#" class="clickable-square square square-17-14"></a>
            <a href="#" class="clickable-square square square-17-15"></a>
            <a href="#" class="clickable-square square square-17-16"></a>
            <a href="#" class="clickable-square square square-17-17"></a>
            <a href="#" class="clickable-square square square-17-18"></a>
            <a href="#" class="clickable-square square square-17-19"></a>

            <a href="#" class="clickable-square square square-18-12"></a>
            <a href="#" class="clickable-square square square-18-13"></a>
            <a href="#" class="clickable-square square square-18-14"></a>
            <a href="#" class="clickable-square square square-18-15"></a>
            <a href="#" class="clickable-square square square-18-16"></a>
            <a href="#" class="clickable-square square square-18-17"></a>
            <a href="#" class="clickable-square square square-18-18"></a>
            <a href="#" class="clickable-square square square-18-19 door"></a>

            <a href="#" class="clickable-square square square-19-12"></a>
            <a href="#" class="clickable-square square square-19-13"></a>
            <a href="#" class="clickable-square square square-19-14"></a>
            <a href="#" class="clickable-square square square-19-15"></a>
            <a href="#" class="clickable-square square square-19-16"></a>
            <a href="#" class="clickable-square square square-19-17"></a>
            <a href="#" class="clickable-square square square-19-18"></a>
            <a href="#" class="clickable-square square square-19-19"></a>

            <a href="#" class="clickable-square square square-20-12"></a>
            <a href="#" class="clickable-square square square-20-13"></a>
            <a href="#" class="clickable-square square square-20-14"></a>
            <a href="#" class="clickable-square square square-20-15"></a>
            <a href="#" class="clickable-square square square-20-16"></a>
            <a href="#" class="clickable-square square square-20-17"></a>
            <a href="#" class="clickable-square square square-20-18"></a>
            <a href="#" class="clickable-square square square-20-19"></a>

            <a href="#" class="clickable-square square square-21-12"></a>
            <a href="#" class="clickable-square square square-21-13"></a>
            <a href="#" class="clickable-square square square-21-14"></a>
            <a href="#" class="clickable-square square square-21-15"></a>
            <a href="#" class="clickable-square square square-21-16"></a>
            <a href="#" class="clickable-square square square-21-17"></a>
            <a href="#" class="clickable-square square square-21-18"></a>
            <a href="#" class="clickable-square square square-21-19"></a>

            <a href="#" class="clickable-square square square-22-13"></a>
            <a href="#" class="clickable-square square square-22-14"></a>
            <a href="#" class="clickable-square square square-22-15"></a>
            <a href="#" class="clickable-square square square-22-16"></a>
            <a href="#" class="clickable-square square square-22-17"></a>
            <a href="#" class="clickable-square square square-22-18"></a>
            <a href="#" class="clickable-square square square-22-19"></a>

            <a href="#" class="clickable-square square square-23-13"></a>
            <a href="#" class="clickable-square square square-23-14"></a>
            <a href="#" class="clickable-square square square-23-15"></a>
            <a href="#" class="clickable-square square square-23-16"></a>
            <a href="#" class="clickable-square square square-23-17"></a>

            <a href="#" class="clickable-square square square-24-13"></a>
            <a href="#" class="clickable-square square square-24-14"></a>
            <a href="#" class="clickable-square square square-24-15"></a>
            <a href="#" class="clickable-square square square-24-16"></a>
            <a href="#" class="clickable-square square square-24-17"></a>

            <a href="#" class="clickable-square square square-25-13 door"></a>
            <a href="#" class="clickable-square square square-25-14"></a>
            <a href="#" class="clickable-square square square-25-15"></a>
            <a href="#" class="clickable-square square square-25-16"></a>
            <a href="#" class="clickable-square square square-25-17"></a>

            <a href="#" class="clickable-square square square-26-13"></a>
            <a href="#" class="clickable-square square square-26-14"></a>
            <a href="#" class="clickable-square square square-26-15"></a>
            <a href="#" class="clickable-square square square-26-16"></a>
            <a href="#" class="clickable-square square square-26-17 door"></a>

            <a href="#" class="clickable-square square square-27-13"></a>
            <a href="#" class="clickable-square square square-27-14"></a>
            <a href="#" class="clickable-square square square-27-15"></a>
            <a href="#" class="clickable-square square square-27-16"></a>
            <a href="#" class="clickable-square square square-27-17"></a>

            <a href="#" class="clickable-square square square-28-13"></a>
            <a href="#" class="clickable-square square square-28-14"></a>
            <a href="#" class="clickable-square square square-28-15"></a>
            <a href="#" class="clickable-square square square-28-16"></a>
            <a href="#" class="clickable-square square square-28-17"></a>

            <a href="#" class="clickable-square square square-29-13"></a>
            <a href="#" class="clickable-square square square-29-14"></a>
            <a href="#" class="clickable-square square square-29-15"></a>
            <a href="#" class="clickable-square square square-29-16"></a>
            <a href="#" class="clickable-square square square-29-17"></a>

            <!-- Continue to create all clickable squares -->
            <a href="#" class="clickable-square square square-930"></a>
            <div id="game-message"></div>
            <button id="next-turn-button" class="btn-yellow" disabled>Suivant</button>

        </div>
    </center>
    <div id="timer">00:00:00</div>
    <div id="dice-container">
        <div id="dice1" class="dice">🎲</div>
        <div id="dice2" class="dice">🎲</div>
    </div>
    <script>
        let nb_joueurs = <?php echo $nb_joueurs; ?>;
        let pionPositions = {};
        let pionIds = [];
        let currentPlayer = 1;
        let moveRange = 0;
        let diceRolled = false;
        let pionInRoom = {}; // Pour suivre si un pion est dans une pièce
        let pionRoomDoor = {}; // Pour suivre quelle porte est associée au pion dans la pièce
        let checkGameInterval;

        let timerInterval; // Pour stocker l'intervalle du timer
        let timeElapsed = 0; // Temps écoulé en secondes
        let timerStarted = false; // Pour vérifier si le timer a déjà commencé

        // Fonction pour formater le temps en HH:MM:SS
        function formatTime(seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            let secs = seconds % 60;

            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Fonction pour démarrer le timer
        function startTimer() {
            timerInterval = setInterval(function() {
                timeElapsed++;
                document.getElementById('timer').textContent = formatTime(timeElapsed);
            }, 1000);
        }

        function checkGameStatus() {
            const partieId = <?php echo $id_partie; ?>;

            console.log("Vérification de l'état de la partie"); // Ajouté pour confirmer l'exécution

            fetch(`check_game_status.php?partie=${partieId}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Données reçues : ", data); // Vérifiez ce que fetch reçoit
                    if (data.si_finit == 1) {
                        console.log("La partie est terminée"); // Confirmation que la condition est remplie
                        clearInterval(checkGameInterval); // Arrêter la vérification continue
                        alert("La partie est terminée !");
                        disableGame(); // Fonction pour désactiver le jeu
                    }
                })
                .catch(error => console.error('Erreur lors de la vérification du statut de la partie:', error));
        }

        function disableGame() {
            console.log("Désactivation du jeu..."); // Ajouté pour confirmer l'exécution
            document.getElementById('dice-container').style.pointerEvents = 'none';
            document.querySelectorAll('.clickable-square').forEach(square => {
                square.style.pointerEvents = 'none';
            });

            const gameMessage = document.getElementById('game-message');
            gameMessage.textContent = "La partie est terminée.";

            console.log("Le jeu est maintenant désactivé."); // Confirmation finale
        }


        const roomCenters = {
            'room-1': {
                doors: [{
                        door: '.square-2-12',
                        positions: [{
                                x: 100,
                                y: 120
                            }, {
                                x: 140,
                                y: 120
                            }, {
                                x: 180,
                                y: 120
                            },
                            {
                                x: 100,
                                y: 160
                            }, {
                                x: 140,
                                y: 160
                            }, {
                                x: 180,
                                y: 160
                            }
                        ]
                    },
                    {
                        door: '.square-9-12',
                        positions: [{
                                x: 100,
                                y: 140
                            }, {
                                x: 140,
                                y: 140
                            }, {
                                x: 180,
                                y: 140
                            },
                            {
                                x: 100,
                                y: 180
                            }, {
                                x: 140,
                                y: 180
                            }, {
                                x: 180,
                                y: 180
                            }
                        ]
                    }
                ]
            },
            'room-2': {
                door: '.square-14-12',
                positions: [{
                        x: 100,
                        y: 290
                    }, {
                        x: 140,
                        y: 290
                    }, {
                        x: 180,
                        y: 290
                    },
                    {
                        x: 100,
                        y: 330
                    }, {
                        x: 140,
                        y: 330
                    }, {
                        x: 180,
                        y: 330
                    }
                ]
            },
            'room-3': {
                door: '.square-25-13',
                positions: [{
                        x: 120,
                        y: 470
                    }, {
                        x: 160,
                        y: 470
                    }, {
                        x: 200,
                        y: 470
                    },
                    {
                        x: 120,
                        y: 510
                    }, {
                        x: 160,
                        y: 510
                    }, {
                        x: 200,
                        y: 510
                    }
                ]
            },
            'room-4': {
                door: '.square-5-16',
                positions: [{
                        x: 480,
                        y: 70
                    }, {
                        x: 520,
                        y: 70
                    }, {
                        x: 560,
                        y: 70
                    },
                    {
                        x: 480,
                        y: 110
                    }, {
                        x: 520,
                        y: 110
                    }, {
                        x: 560,
                        y: 110
                    }
                ]
            },
            'room-5': {
                door: '.square-11-17',
                positions: [{
                        x: 480,
                        y: 230
                    }, {
                        x: 520,
                        y: 230
                    }, {
                        x: 560,
                        y: 230
                    },
                    {
                        x: 480,
                        y: 270
                    }, {
                        x: 520,
                        y: 270
                    }, {
                        x: 560,
                        y: 270
                    }
                ]
            },
            'room-6': {
                door: '.square-18-19',
                positions: [{
                        x: 480,
                        y: 350
                    }, {
                        x: 520,
                        y: 350
                    }, {
                        x: 560,
                        y: 350
                    },
                    {
                        x: 480,
                        y: 390
                    }, {
                        x: 520,
                        y: 390
                    }, {
                        x: 560,
                        y: 390
                    }
                ]
            },
            'room-7': {
                door: '.square-26-17',
                positions: [{
                        x: 480,
                        y: 490
                    }, {
                        x: 520,
                        y: 490
                    }, {
                        x: 560,
                        y: 490
                    },
                    {
                        x: 480,
                        y: 530
                    }, {
                        x: 520,
                        y: 530
                    }, {
                        x: 560,
                        y: 530
                    }
                ]
            }
        };


        // Initialisation des pions
        for (let i = 1; i <= nb_joueurs; i++) {
            pionIds.push('pion-' + i);
            pionInRoom['pion-' + i] = false; // Initialiser chaque pion comme étant hors de toute pièce
        }

        // Fonction pour obtenir une case aléatoire parmi les cases cliquables
        function getRandomSquare() {
            const squares = document.querySelectorAll('.clickable-square');
            const randomIndex = Math.floor(Math.random() * squares.length);
            return squares[randomIndex].classList[2]; // Récupère la classe unique de la case (par exemple, "square-1-12")
        }

        // Fonction pour placer un pion sur une case aléatoire
        function placePionRandomly(pionId) {
            const randomSquareClass = getRandomSquare();
            placePion(pionId, `.${randomSquareClass}`);
        }

        // Fonction pour placer un pion sur une case spécifique
        function placePion(pionId, squareClass) {
            const square = document.querySelector(squareClass);
            const rect = square.getBoundingClientRect();
            const pion = document.getElementById(pionId);
            const container = document.querySelector('.container');

            pion.style.left = `${rect.left - container.offsetLeft}px`;
            pion.style.top = `${rect.top - container.offsetTop}px`;

            pionPositions[pionId] = squareClass; // Mise à jour de la position du pion
        }

        // Fonction pour lancer les dés
        // Fonction pour lancer les dés
        function rollDice() {
            if (!diceRolled) { // Ne lancer les dés qu'une seule fois par tour
                const dice1 = document.getElementById('dice1');
                const dice2 = document.getElementById('dice2');

                // Démarrer le timer lors du premier lancement de dé
                if (!timerStarted) {
                    startTimer();
                    timerStarted = true;
                }

                // Ajouter la classe 'rolling' pour l'animation
                dice1.classList.add('rolling');
                dice2.classList.add('rolling');

                // Simuler l'animation pendant 1.5 secondes
                setTimeout(function() {
                    const diceRoll1 = Math.floor(Math.random() * 6) + 1;
                    const diceRoll2 = Math.floor(Math.random() * 6) + 1;
                    dice1.textContent = diceRoll1;
                    dice2.textContent = diceRoll2;

                    moveRange = diceRoll1 + diceRoll2;

                    const currentPionId = pionIds[currentPlayer - 1];

                    if (pionInRoom[currentPionId]) {
                        // Si le pion est dans une pièce, utiliser la porte pour calculer les déplacements
                        const doorClass = pionRoomDoor[currentPionId];
                        highlightAvailableMoves(doorClass, moveRange);
                    } else {
                        // Sinon, utiliser la position actuelle du pion
                        highlightAvailableMoves(pionPositions[currentPionId], moveRange);
                    }
                    diceRolled = true; // Désactiver le lancer de dés jusqu'au prochain tour
                    dice1.classList.remove('rolling');
                    dice2.classList.remove('rolling');
                    setTimeout(function() {
                        dice1.textContent = "🎲";
                        dice2.textContent = "🎲";
                    }, 3000); // Attendre 3 secondes avant de revenir à l'icône des dés
                }, 1500);
            }
        }

        // Changer de tour
        function nextTurn() {
            currentPlayer = (currentPlayer % nb_joueurs) + 1; // Passer au joueur suivant
            diceRolled = false; // Réinitialiser pour permettre le lancer des dés au prochain tour
            document.querySelector('#next-turn-button').disabled = true;
            updateGameMessage();
        }

        // Mise à jour du message de jeu
        function updateGameMessage() {
            const gameMessage = document.getElementById('game-message');
            gameMessage.textContent = `C'est le tour du joueur  ${currentPlayer}`;
        }

        // Fonction pour surligner les cases accessibles autour du pion actif avec une portée donnée
        function highlightAvailableMoves(squareClass, range) {
            const allSquares = document.querySelectorAll('.clickable-square');
            const currentSquare = document.querySelector(squareClass);

            const currentRect = currentSquare.getBoundingClientRect();
            const currentX = currentRect.left;
            const currentY = currentRect.top;

            allSquares.forEach(square => {
                square.classList.remove('highlight', 'clickable');
                const squareRect = square.getBoundingClientRect();
                const squareX = squareRect.left;
                const squareY = squareRect.top;

                const deltaX = Math.abs(squareX - currentX) / squareRect.width;
                const deltaY = Math.abs(squareY - currentY) / squareRect.height;

                // Si la case est dans la portée, on la rend cliquable
                if (deltaX + deltaY <= range) {
                    square.classList.add('highlight');
                    square.classList.add('clickable'); // Rendre cliquable uniquement les cases dans la portée
                }
            });
        }

        // Fonction pour désactiver toutes les cases cliquables
        function disableClickableSquares() {
            document.querySelectorAll('.clickable-square').forEach(square => {
                square.classList.remove('highlight', 'clickable');
            });
        }

        function getRoomFromDoor(squareClass) {
            for (const room in roomCenters) {
                const roomData = roomCenters[room];
                if (Array.isArray(roomData.doors)) {
                    for (const doorData of roomData.doors) {
                        if (doorData.door === squareClass) {
                            return room;
                        }
                    }
                } else if (roomData.door === squareClass) {
                    return room;
                }
            }
            return null;
        }


        // Fonction pour déplacer un pion au centre d'une pièce
        function moveToRoomCenter(pionId, room) {
            const pion = document.getElementById(pionId);
            const roomCenter = roomCenters[room];
            const joueurId = pionId.split('-')[1];
            const partieId = <?php echo $id_partie; ?>;

            // Trouver une position libre dans la pièce
            const availablePositions = roomCenter.positions.filter(pos =>
                !Object.values(pionPositions).includes(JSON.stringify(pos))
            );

            if (availablePositions.length === 0) {
                console.error("Aucune position libre dans la pièce.");
                return;
            }

            const position = availablePositions[0]; // Choisir la première position libre
            pion.style.left = `${position.x}px`;
            pion.style.top = `${position.y}px`;

            pionPositions[pionId] = JSON.stringify(position);
            pionInRoom[pionId] = true;
            pionRoomDoor[pionId] = roomCenter.door;

            // Mettre à jour la base de données
            fetch(`update_player_room_status.php?joueur=${joueurId}&id_partie=${partieId}&dans_piece=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Statut de la pièce mis à jour avec succès.");
                    } else {
                        console.error("Erreur lors de la mise à jour : ", data.error);
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour :', error));
        }


        // Initialiser les positions des pions au chargement
        window.onload = function() {
            checkGameInterval = setInterval(checkGameStatus, 5000);
            for (let i = 1; i <= nb_joueurs; i++) {
                placePionRandomly('pion-' + i); // Placer chaque pion aléatoirement
            }
            highlightAvailableMoves(pionPositions[pionIds[currentPlayer - 1]], moveRange); // Mettre en surbrillance autour du pion actuel
            updateGameMessage();
        };

        // Ajouter un événement pour lancer les dés
        document.getElementById('dice-container').addEventListener('click', rollDice);

        document.addEventListener('DOMContentLoaded', (event) => {
            // Assure-toi que le DOM est entièrement chargé avant d'essayer d'ajouter des écouteurs d'événements
            const nextTurnButton = document.getElementById('next-turn-button');
            if (nextTurnButton) {
                nextTurnButton.addEventListener('click', nextTurn);
            }
        });

        // Ajouter un événement pour déplacer le pion au clic sur une case
        document.querySelectorAll('.clickable-square').forEach(square => {
            square.addEventListener('click', function() {
                const currentPionId = pionIds[currentPlayer - 1];
                if (this.classList.contains('clickable') && diceRolled) { // Vérifier si les cases sont cliquables
                    const squareClass = `.${this.classList[2]}`;
                    const room = getRoomFromDoor(squareClass);
                    if (room) {
                        // Si le joueur clique sur une porte, le pion entre dans la pièce
                        moveToRoomCenter(currentPionId, room);
                    } else {
                        // Déplacer le pion sur la case cliquée
                        placePion(currentPionId, squareClass);
                        pionInRoom[currentPionId] = false; // Le pion est hors de toute pièce
                    }
                    moveRange = 0; // Réinitialiser la portée
                    disableClickableSquares(); // Désactiver toutes les cases après un tour
                    document.querySelector('#next-turn-button').disabled = false; // Réactiver le bouton

                    //nextTurn(); // Passer au joueur suivant après le déplacement
                }
            });
        });
    </script>