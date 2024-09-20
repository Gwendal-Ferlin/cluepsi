<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_db = 'cluepsi_bdd';

// Connexion à la base de données
$connexion = new mysqli($db_host, $db_user, $db_password, $db_db);

if ($connexion->connect_error) {
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_partie = $_POST['code_partie'];

    // Vérification du format du code de la partie
    if (preg_match('/^(\d+)-(\d+)$/', $code_partie, $matches)) {
        $id_partie = (int)$matches[1];
        $numero_joueur = (int)$matches[2];

        // Requête pour vérifier si la partie existe et si elle n'est pas terminée
        $sql_partie = "SELECT p.si_finit FROM partie p
                       JOIN joueur j ON p.id_partie = j.id_partie
                       WHERE p.id_partie = ? AND j.numéro_joueur = ?";
        $stmt_partie = $connexion->prepare($sql_partie);
        $stmt_partie->bind_param('ii', $id_partie, $numero_joueur);
        $stmt_partie->execute();
        $result_partie = $stmt_partie->get_result();

        if ($result_partie->num_rows > 0) {
            $row = $result_partie->fetch_assoc();
            if ($row['si_finit'] == 0) {
                // La partie est active, redirection vers la page de jeu
                header('Location: page_joueur.php?partie=' . $id_partie . '&joueur=' . $numero_joueur);
                exit();
            } else {
                $erreur = 'Cette partie est déjà terminée.';
            }
        } else {
            $erreur = 'Code de partie incorrect.';
        }

        $stmt_partie->close();
    } else {
        $erreur = 'Code de partie incorrect.';
    }
}

$connexion->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejoindre une partie</title>
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
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .erreur {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            h2 {
                font-size: 24px;
            }

            .container {
                padding: 20px;
            }

            input[type="text"], input[type="submit"] {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Rejoindre une partie</h2>

    <form method="POST" action="">
        <input type="text" name="code_partie" placeholder="Entrez le code de la partie" required>
        <?php
        if ($erreur) {
            echo "<div class='erreur'>$erreur</div>";
        }
        ?>
        <input type="submit" value="Rejoindre la partie">
    </form>
</div>

</body>
</html>
