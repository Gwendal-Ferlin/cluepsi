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

// Récupérer l'ID de la partie et le numéro du joueur depuis l'URL
$id_partie = isset($_GET['partie']) ? (int)$_GET['partie'] : 0;
$numero_joueur = isset($_GET['joueur']) ? (int)$_GET['joueur'] : 0;

$response = ['success' => false, 'dans_piece' => 0];

if ($id_partie > 0 && $numero_joueur > 0) {
    // Requête pour récupérer le joueur et vérifier s'il est dans une pièce
    $sql_joueur = "
        SELECT dans_piece 
        FROM joueur 
        WHERE id_partie = ? AND numéro_joueur = ?
    ";
    $stmt_joueur = $conn->prepare($sql_joueur);
    $stmt_joueur->bind_param("ii", $id_partie, $numero_joueur);
    $stmt_joueur->execute();
    $result_joueur = $stmt_joueur->get_result();
    
    if ($result_joueur->num_rows > 0) {
        $row_joueur = $result_joueur->fetch_assoc();
        $response['success'] = true;
        $response['dans_piece'] = (int)$row_joueur['dans_piece'];
    }

    $stmt_joueur->close();
}

$conn->close();

// Renvoyer la réponse au format JSON
echo json_encode($response);
