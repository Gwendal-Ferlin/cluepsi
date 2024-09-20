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

// Récupérer les paramètres envoyés via l'URL
$numero_joueur = isset($_GET['joueur']) ? (int)$_GET['joueur'] : 0;
$id_partie = isset($_GET['id_partie']) ? (int)$_GET['id_partie'] : 0;
$dans_piece = isset($_GET['dans_piece']) ? (int)$_GET['dans_piece'] : 0;

if ($numero_joueur > 0 && $id_partie > 0) {
    // Requête pour mettre à jour la colonne dans_piece du joueur
    $sql = "UPDATE joueur SET dans_piece = ? WHERE numéro_joueur = ? AND id_partie = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $dans_piece, $numero_joueur, $id_partie);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Paramètres manquants"]);
}

$conn->close();
?>
