<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CluEpsi - Accueil</title>
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
            padding: 50px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 30px;
            color: #333;
        }

        label {
            font-size: 18px;
            color: #555;
        }

        .button-group {
            margin-top: 30px;
        }

        .button-group a,
        .button-group input[type="submit"] {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            margin: 10px;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-group a:hover,
        .button-group input[type="submit"]:hover {
            background-color: #0056b3;
        }

        select {
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 28px;
            }

            .container {
                padding: 20px;
            }

            .button-group a,
            .button-group input[type="submit"] {
                padding: 8px 16px;
                font-size: 14px;
            }

            select {
                font-size: 14px;
                padding: 8px;
            }

            input[type="submit"] {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Bienvenue sur CluEpsi</h1>

    <!-- Formulaire pour choisir le nombre de joueurs -->
    <form action="creer_partie_locale.php" method="POST">
        <label for="nombre_joueurs">Choisissez le nombre de joueurs (entre 3 et 6) :</label><br><br>
        <select name="nombre_joueurs" id="nombre_joueurs" required>
            <option value="3">3 joueurs</option>
            <option value="4">4 joueurs</option>
            <option value="5">5 joueurs</option>
            <option value="6">6 joueurs</option>
        </select><br><br>
        <input type="submit" value="Créer une partie locale">
    </form>

    <div class="button-group">
        <!-- Lien pour créer une partie en ligne -->
        <a href="creer_partie_en_ligne.php">Créer une partie en ligne</a>
        <!-- Lien pour rejoindre une partie -->
        <a href="rejoindre_local.php">Rejoindre une partie</a>
    </div>
</div>

</body>
</html>
