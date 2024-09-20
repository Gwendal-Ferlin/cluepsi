<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_db = 'cluepsi_bdd';

// Connexion à la base de données
$connexion = new mysqli($db_host, $db_user, $db_password, $db_db);

if ($connexion->connect_error) {
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);
}

// Vérification que le formulaire a été soumis avec un nombre de joueurs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_joueurs'])) {
    $nombre_joueurs = (int)$_POST['nombre_joueurs'];

    // 1. Création de la nouvelle partie
    $sql = "INSERT INTO partie (nombre_joueur, si_finit) VALUES (?, 0)";
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param("i", $nombre_joueurs);
    if (!$stmt->execute()) {
        die("Erreur lors de la création de la partie : " . $stmt->error);
    }

    // Récupérer l'ID de la partie créée
    $id_partie = $stmt->insert_id;

    // Vérification que la partie a été créée correctement
    if ($id_partie) {
        
    } else {
        die("Erreur : Impossible de créer la partie.");
    }

    // 2. Ajout des joueurs dans la base de données
    $codes_joueurs = [];
    for ($i = 1; $i <= $nombre_joueurs; $i++) {
        // Générer un code pour chaque joueur
        $code_joueur = $id_partie . '-' . $i;  // Ex : "1-1", "1-2", etc.
        $codes_joueurs[] = $code_joueur;

        // Insérer chaque joueur dans la table 'joueur'
        $sql_joueur = "INSERT INTO joueur (numéro_joueur, id_partie) VALUES (?, ?)";
        $stmt_joueur = $connexion->prepare($sql_joueur);
        $stmt_joueur->bind_param("ii", $i, $id_partie);
        if (!$stmt_joueur->execute()) {
            die("Erreur lors de l'insertion du joueur $i : " . $stmt_joueur->error);
        }
    }

    // Vérification de l'ajout des joueurs

    // 3. Génération des cartes mystères pour la partie
    $roles = ['matiere', 'salle', 'prof'];
    $cartes_mystere = [];

    foreach ($roles as $role) {
        $sql = "SELECT id_carte FROM carte WHERE role = ? ORDER BY RAND() LIMIT 1";
        $stmt = $connexion->prepare($sql);
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $carte = $result->fetch_assoc();

        // Ajouter la carte mystère et l'insérer dans la table 'carte_mystere'
        $cartes_mystere[] = $carte['id_carte'];
        $sql_mystere = "INSERT INTO carte_mystere (id_partie, id_carte) VALUES (?, ?)";
        $stmt_mystere = $connexion->prepare($sql_mystere);
        $stmt_mystere->bind_param("ii", $id_partie, $carte['id_carte']);
        $stmt_mystere->execute();
    }

    // Distribution des cartes restantes aux joueurs
    $placeholders = implode(',', array_fill(0, count($cartes_mystere), '?'));
    $types = str_repeat('i', count($cartes_mystere));

    $sql_restant = "SELECT id_carte FROM carte WHERE id_carte NOT IN ($placeholders)";
    $stmt_restant = $connexion->prepare($sql_restant);
    $stmt_restant->bind_param($types, ...$cartes_mystere);
    $stmt_restant->execute();
    $result_restant = $stmt_restant->get_result();

    $cartes_restantes = [];
    while ($carte = $result_restant->fetch_assoc()) {
        $cartes_restantes[] = $carte['id_carte'];
    }

    shuffle($cartes_restantes);

    // Distribution équitable des cartes restantes aux joueurs
    $joueur = 1;
    foreach ($cartes_restantes as $id_carte) {
        $sql_joueur = "SELECT id_joueur FROM joueur WHERE id_partie = ? AND numéro_joueur = ?";
        $stmt_joueur = $connexion->prepare($sql_joueur);
        $stmt_joueur->bind_param("ii", $id_partie, $joueur);
        $stmt_joueur->execute();
        $result_joueur = $stmt_joueur->get_result();
        $id_joueur = $result_joueur->fetch_assoc()['id_joueur'];

        if (!$id_joueur) {
            die("Erreur : Joueur non trouvé pour la partie.");
        }

        $sql_inventaire = "INSERT INTO inventaire (id_joueur, id_carte) VALUES (?, ?)";
        $stmt_inventaire = $connexion->prepare($sql_inventaire);
        $stmt_inventaire->bind_param("ii", $id_joueur, $id_carte);
        $stmt_inventaire->execute();

        $joueur = ($joueur % $nombre_joueurs) + 1;
    }

    // Fermeture de la connexion à la base de données
    $connexion->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CluEpsi - Codes des joueurs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f5d0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #555;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
        }

        ul li {
            background-color: #f0f0f0;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            color: #333;
            font-weight: bold;
        }

        .button {
            padding: 10px 20px;
            background-color: #d1b18a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .button:hover {
            background-color: #b5926b;
        }

        a.button {
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Partie créée avec succès !</h1>
    <h2>Voici les codes pour rejoindre la partie :</h2>
    <ul>
        <?php if (!empty($codes_joueurs)) : ?>
            <?php foreach ($codes_joueurs as $code) : ?>
                <li>Code du joueur : <strong><?php echo htmlspecialchars($code); ?></strong></li>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Aucun code de joueur n'a été généré.</p>
        <?php endif; ?>
    </ul>

    <form action="plato.php" method="GET">
        <input type="hidden" name="partie" value="<?php echo $id_partie; ?>">
        <input type="submit" value="Lancer la partie" class="button">
    </form>
</div>

</body>
</html>
