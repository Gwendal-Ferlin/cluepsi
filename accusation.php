
<?php
$servername = "localhost";  // Adresse du serveur
$username = "root";         // Nom d'utilisateur
$password = "";         // Mot de passe MAMP
$dbname = "cluepsi_bdd";    // Nom de la base de données

// Activer l'affichage des erreurs pour débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la BDD
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les paramètres de l'URL
$id_partie = isset($_GET['partie']) ? (int)$_GET['partie'] : 0;
$num_joueur = isset($_GET['joueur']) ? (int)$_GET['joueur'] : 0;

// Vérifier que les paramètres sont valides
if ($id_partie <= 0 || $num_joueur <= 0) {
    die("Invalid parameters.");
}

// Requête pour récupérer les cartes par rôle
$sql = "SELECT id_carte, nom_carte, role FROM carte";
$result = $conn->query($sql);

$cartes = [
    'prof' => [],
    'salle' => [],
    'matiere' => []
];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cartes[$row['role']][] = $row;
    }
}

// Traitement de l'accusation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accusation_prof = $_POST['accusation_prof'] ?? '';
    $accusation_salle = $_POST['accusation_salle'] ?? '';
    $accusation_matiere = $_POST['accusation_matiere'] ?? '';

    // Récupérer les cartes mystères pour la partie
    $sql_mystere = "SELECT carte.id_carte, carte.role 
                    FROM carte_mystere 
                    JOIN carte ON carte_mystere.id_carte = carte.id_carte 
                    WHERE carte_mystere.id_partie = ?";

    // Préparer la requête
    $stmt = $conn->prepare($sql_mystere);

    // Vérifier si la préparation a réussi
    if ($stmt === false) {
        die("Erreur dans la préparation de la requête SQL : " . $conn->error);
    }

    // Associer le paramètre
    $stmt->bind_param("i", $id_partie);
    $stmt->execute();

    // Récupérer le résultat
    $result_mystere = $stmt->get_result();

    // Vérifier si le résultat est valide
    if ($result_mystere === false) {
        die("Erreur dans l'exécution de la requête SQL : " . $stmt->error);
    }

    $cartes_mystere = [
        'prof' => '',
        'salle' => '',
        'matiere' => ''
    ];

    // Stocker les cartes mystères
    if ($result_mystere->num_rows > 0) {
        while ($row_mystere = $result_mystere->fetch_assoc()) {
            $cartes_mystere[$row_mystere['role']] = $row_mystere['id_carte'];
        }
    }

    // Comparer les cartes sélectionnées avec les cartes mystères
    if ($accusation_prof == $cartes_mystere['prof'] &&
        $accusation_salle == $cartes_mystere['salle'] &&
        $accusation_matiere == $cartes_mystere['matiere']) {

        echo "<p>Partie gagnée !</p>";

        // Requête pour mettre à jour la partie, définir si_finit à 1
        $sql_update = "UPDATE partie SET si_finit = 1 WHERE id_partie = ?";
        $stmt_update = $conn->prepare($sql_update);

        // Vérifier si la préparation a réussi
        if ($stmt_update === false) {
            die("Erreur dans la préparation de la requête SQL : " . $conn->error);
        }

        // Associer le paramètre id_partie
        $stmt_update->bind_param("i", $id_partie);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            echo "<p>La partie a été mise à jour avec succès !</p>";
        } else {
            echo "<p>Échec de la mise à jour de la partie.</p>";
        }

        $stmt_update->close();

        // Automatiquement cliquer sur le bouton "Revenir" après la victoire
        echo "<script>
                setTimeout(function() {
                    document.querySelector('.button').click();
                }, 2000); // Délai de 2 secondes avant de revenir
              </script>";
    } else {
        echo "<p>Mauvaise réponse !</p>";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accusation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f5d0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        a.button {
            text-decoration: none;
            color: #fff;
            background-color: #d1b18a;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            cursor: pointer;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .card-group {
            margin-bottom: 20px;
        }
        .card-group h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .card-group img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin: 5px;
            cursor: pointer;
            display: inline-block;
            transition: opacity 0.3s, border 0.3s;
        }
        .card-group img.hidden {
            opacity: 0.2;
            pointer-events: none;
        }
        .card-group img.selected {
            border: 2px solid #007bff;
        }
        .selected-cards {
            margin-top: 20px;
        }
        .selected-cards div {
            margin-bottom: 10px;
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d1b18a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        .button:hover {
            background-color: #b5926b;
        }
        .reset-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff6b6b;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            color: #fff;
        }
        .reset-button:hover {
            background-color: #ff4d4d;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Accuser les cartes</h1>
    <form method="POST" id="accusationForm">
        <div class="card-group" id="group_prof">
            <h2>Choisissez une carte Prof</h2>
            <?php foreach ($cartes['prof'] as $carte): ?>
                <img src="<?php echo $carte['id_carte']; ?>.png" alt="<?php echo $carte['nom_carte']; ?>"
                     onclick="selectCard('prof', '<?php echo $carte['id_carte']; ?>', this)">
            <?php endforeach; ?>
            <input type="hidden" name="accusation_prof" id="selected_prof">
        </div>

        <div class="card-group" id="group_salle">
            <h2>Choisissez une carte Salle</h2>
            <?php foreach ($cartes['salle'] as $carte): ?>
                <img src="<?php echo $carte['id_carte']; ?>.png" alt="<?php echo $carte['nom_carte']; ?>"
                     onclick="selectCard('salle', '<?php echo $carte['id_carte']; ?>', this)">
            <?php endforeach; ?>
            <input type="hidden" name="accusation_salle" id="selected_salle">
        </div>

        <div class="card-group" id="group_matiere">
            <h2>Choisissez une carte Matière</h2>
            <?php foreach ($cartes['matiere'] as $carte): ?>
                <img src="<?php echo $carte['id_carte']; ?>.png" alt="<?php echo $carte['nom_carte']; ?>"
                     onclick="selectCard('matiere', '<?php echo $carte['id_carte']; ?>', this)">
            <?php endforeach; ?>
            <input type="hidden" name="accusation_matiere" id="selected_matiere">
        </div>

        <div class="selected-cards" hidden>
            <h3>Cartes sélectionnées</h3>
            <div id="selected_prof_display">Carte Prof : <span id="selected_prof_name">Aucune</span></div>
            <div id="selected_salle_display">Carte Salle : <span id="selected_salle_name">Aucune</span></div>
            <div id="selected_matiere_display">Carte Matière : <span id="selected_matiere_name">Aucune</span></div>
        </div>

        <button type="submit" class="button">Faire l'accusation</button>
        <button type="button" class="reset-button" onclick="resetSelection()">Réinitialiser les sélections</button>
        <a href="page_joueur.php?partie=<?php echo $id_partie; ?>&joueur=<?php echo $num_joueur; ?>" class="button">Revenir</a>
    </form>
</div>

<script>
    function selectCard(role, id, element) {
        // Met à jour le champ caché
        document.getElementById('selected_' + role).value = id;

        // Met à jour l'affichage des cartes sélectionnées
        document.getElementById('selected_' + role + '_name').innerText = id;

        // Cache toutes les autres images du même groupe de rôle
        const allImages = document.querySelectorAll(`#group_${role} img`);
        allImages.forEach(img => {
            if (img !== element) {
                img.classList.add('hidden');
            } else {
                img.classList.add('selected');
            }
        });
    }

    function resetSelection() {
        // Réinitialise toutes les sélections et affiche toutes les cartes
        document.querySelectorAll('.card-group img').forEach(img => {
            img.classList.remove('hidden', 'selected');
        });
        document.getElementById('selected_prof').value = '';
        document.getElementById('selected_salle').value = '';
        document.getElementById('selected_matiere').value = '';
        document.getElementById('selected_prof_name').innerText = 'Aucune';
        document.getElementById('selected_salle_name').innerText = 'Aucune';
        document.getElementById('selected_matiere_name').innerText = 'Aucune';
    }
</script>

</body>
</html>
