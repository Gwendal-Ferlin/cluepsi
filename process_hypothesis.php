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

// Récupérer les données POST de l'hypothèse
$id_joueur = isset($_POST['id_joueur']) ? (int)$_POST['id_joueur'] : 0;
$id_partie = isset($_POST['id_partie']) ? (int)$_POST['id_partie'] : 0;
$prof = isset($_POST['prof']) ? (int)$_POST['prof'] : 0;
$salle = isset($_POST['salle']) ? (int)$_POST['salle'] : 0;
$matiere = isset($_POST['matiere']) ? (int)$_POST['matiere'] : 0;

// S'assurer que toutes les données nécessaires sont présentes
if ($id_joueur > 0 && $id_partie > 0 && $prof > 0 && $salle > 0 && $matiere > 0) {
    
    // Récupérer tous les joueurs de la partie (sauf celui qui fait l'hypothèse)
    $sql_joueurs = "SELECT id_joueur, numéro_joueur FROM joueur WHERE id_partie = ? AND id_joueur != ?";
    $stmt_joueurs = $conn->prepare($sql_joueurs);
    $stmt_joueurs->bind_param("ii", $id_partie, $id_joueur);
    $stmt_joueurs->execute();
    $result_joueurs = $stmt_joueurs->get_result();
    
    $carte_trouvee = false; // Pour indiquer si une carte a été trouvée par un autre joueur

    // Boucle à travers les autres joueurs pour vérifier leurs cartes
    while ($row_joueur = $result_joueurs->fetch_assoc()) {
        $id_joueur_autre = $row_joueur['id_joueur'];

        // Requête pour vérifier si l'autre joueur a une des cartes de l'hypothèse
        $sql_verif_cartes = "
            SELECT carte.nom_carte 
            FROM carte 
            JOIN inventaire ON carte.id_carte = inventaire.id_carte 
            WHERE inventaire.id_joueur = ? 
            AND carte.id_carte IN (?, ?, ?)
        ";
        $stmt_verif_cartes = $conn->prepare($sql_verif_cartes);
        $stmt_verif_cartes->bind_param("iiii", $id_joueur_autre, $prof, $salle, $matiere);
        $stmt_verif_cartes->execute();
        $result_cartes = $stmt_verif_cartes->get_result();

        // Si l'autre joueur possède une carte
        if ($result_cartes->num_rows > 0) {
            $row_carte = $result_cartes->fetch_assoc();
            $nom_carte = $row_carte['nom_carte'];
            echo "Le joueur {$row_joueur['numéro_joueur']} possède la carte : {$nom_carte}.<br>";
            $carte_trouvee = true;
            break; // Arrêter la boucle car un joueur a montré une carte
        }

        $stmt_verif_cartes->close();
    }

    if (!$carte_trouvee) {
        // Si aucun joueur n'a montré de carte
        echo "Aucun des joueurs ne possède les cartes demandées.";
    }

    $stmt_joueurs->close();
} else {
    echo "Données d'hypothèse manquantes.";
}

$conn->close();
?>

