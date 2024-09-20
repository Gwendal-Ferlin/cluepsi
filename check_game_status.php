<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cluepsi_bdd";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer l'ID de la partie depuis l'URL ou une requête GET
$id_partie = isset($_GET['partie']) ? (int)$_GET['partie'] : 0;

if ($id_partie > 0) {
    // Requête pour obtenir la valeur de si_finit
    $sql = "SELECT si_finit FROM partie WHERE id_partie = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_partie);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['si_finit' => $row['si_finit']]);
    } else {
        echo json_encode(['error' => 'Partie non trouvée']);
    }

    $stmt->close();
}

$conn->close();
