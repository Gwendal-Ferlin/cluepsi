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

$images = [];
$nb_joueurs = 0; // Initialisation du nombre de joueurs
$dans_piece = false; // Initialisation du statut de pièce

if ($id_partie > 0 && $numero_joueur > 0) {
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

    // Requête pour récupérer le joueur et vérifier s'il est dans une pièce
    $sql_joueur = "
        SELECT id_joueur, dans_piece 
        FROM joueur 
        WHERE id_partie = ? AND numéro_joueur = ?
    ";
    $stmt_joueur = $conn->prepare($sql_joueur);
    $stmt_joueur->bind_param("ii", $id_partie, $numero_joueur);
    $stmt_joueur->execute();
    $result_joueur = $stmt_joueur->get_result();
    
    if ($result_joueur->num_rows > 0) {
        $row_joueur = $result_joueur->fetch_assoc();
        $id_joueur = $row_joueur['id_joueur'];
        $dans_piece = (bool)$row_joueur['dans_piece']; // Statut de la pièce

        // Requête pour récupérer les cartes du joueur
        $sql_cartes = "
            SELECT carte.id_carte 
            FROM carte 
            JOIN inventaire ON carte.id_carte = inventaire.id_carte 
            WHERE inventaire.id_joueur = ?
        ";
        $stmt_cartes = $conn->prepare($sql_cartes);
        $stmt_cartes->bind_param("i", $id_joueur);
        $stmt_cartes->execute();
        $result_cartes = $stmt_cartes->get_result();

        if ($result_cartes->num_rows > 0) {
            while ($row = $result_cartes->fetch_assoc()) {
                $images[] = $row['id_carte'] . ".png";  // Utilise l'id de la carte pour nommer l'image
            }
        } else {
            echo "Aucune carte trouvée pour ce joueur.";
        }

        $stmt_cartes->close();
    } else {
        echo "Joueur non trouvé dans cette partie.";
    }

    $stmt_joueur->close();
} else {
    echo "ID de partie ou numéro de joueur manquant.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checklist du Joueur</title>
  <style>
/* Réinitialisation des marges et paddings */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Style du body */
body {
  font-family: Arial, sans-serif;
  background-color: #f8f5d0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.checklist-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

/* Style du conteneur principal */
.container {
  text-align: center;
  background-color: #f8f5d0;
  padding: 20px;
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
}

/* Style du titre */
h1 {
  margin-bottom: 20px;
  font-size: 22px;
  color: #000;
}

/* Style du bouton Checklist */
.checklist-btn {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #d1b18a;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  width: 100%;
  max-width: 200px;
}

.checklist-btn:hover {
  background-color: #b5926b;
}

/* Styles pour la fenêtre modale */
.modal {
  display: none;
  position: fixed;
  z-index: 100;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 10px;
  border-radius: 10px;
  width: 95%;
  max-width: 500px;
  text-align: center;
  overflow: auto;
}

.close-btn {
  background-color: #d1b18a;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 5px;
  margin-top: 10px;
}

.close-btn:hover {
  background-color: #b5926b;
}

/* Style des tables */
table {
  border-collapse: collapse;
  margin: 0 auto;
  font-size: 12px;
  width: 100%;
  max-width: 100%;
}

table, th, td {
  border: 1px solid black;
}

th, td {
  padding: 5px;
  text-align: center;
  cursor: pointer;
}

th {
  background-color: #d1b18a;
  font-weight: bold;
}

/* Style du carrousel */
.carousel {
  position: relative;
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
}

.carousel img {
  width: 100%;
  max-width: 450px;
  height: 300px;
  object-fit: contain;
  margin-top: 20px;
  border-radius: 10px;
}

/* Style des boutons du carrousel */
.carousel button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  padding: 10px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10;
  font-size: 20px;
}

.carousel button.prev {
  left: 10px;
}

.carousel button.next {
  right: 10px;
}

.carousel button:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

.carousel button:focus {
  outline: none;
}
/* Style pour la modale de l'hypothèse */
/* Style pour la modale de l'hypothèse */
#hypothesisModal {
  display: flex;
  position: fixed ;
  z-index: 200;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center ;
}

/* Style du contenu de la modale */
.modal-content {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 500px;
  text-align: center !important;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}


.modal-content h2 {
  font-size: 22px;
  margin-bottom: 15px;
  color: #333;
}

/* Style des labels */
.modal-content label {
  font-size: 14px;
  margin-top: 10px;
  color: #555;
  display: block;
  text-align: left;
}

/* Style des selects */
.modal-content select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  margin-bottom: 15px;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 14px;
  background-color: #f8f8f8;
  color: #333;
  cursor: pointer;
}

/* Style du bouton de validation */
.modal-content button[type="submit"] {
  padding: 10px 20px;
  background-color: #d1b18a;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  color: white;
  margin-top: 10px;
  width: 100%;
}

.modal-content button[type="submit"]:hover {
  background-color: #b5926b;
}

/* Style du bouton de fermeture */
.modal-content .close-btn {
  background-color: #d1b18a;
  border: none;
  padding: 8px 15px;
  cursor: pointer;
  border-radius: 5px;
  margin-top: 15px;
  font-size: 14px;
}

.modal-content .close-btn:hover {
  background-color: #b5926b;
}

/* Style du message de réponse après soumission */
#hypothesis-response {
  margin-top: 20px;
  font-size: 14px;
  color: #333;
  text-align: center;
}

/* Media queries pour les petits écrans */
@media screen and (max-width: 768px) {
  .modal-content {
    width: 90%;
  }

  .modal-content h2 {
    font-size: 18px;
  }

  .modal-content button[type="submit"] {
    font-size: 14px;
  }
}


/* Media queries pour les petits écrans */
@media screen and (max-width: 768px) {
  table, th, td {
    font-size: 10px;
  }

  .checklist-btn {
    font-size: 14px;
    padding: 8px 16px;
  }

  h1 {
    font-size: 18px;
  }
}

@media screen and (max-width: 480px) {
  .modal-content {
    width: 90%;
  }
}

/* Styles pour les différentes étapes (vide, ? et X) */
td.vide {
  background-color: white;
  color: black;
}

td.interrogation {
  background-color: yellow;
  color: black;
}

td.croix {
  background-color: red;
  color: white;
}
  </style>
</head>
<body>

  <div class="container">
    <h1>Joueur <?php print($numero_joueur) ?></h1>

    <!-- Carrousel des images -->
    <div class="carousel">
      <button class="prev" onclick="prevImage()">&#10094;</button>
      <img id="carousel-image" src="" alt="Aucune carte disponible">
      <button class="next" onclick="nextImage()">&#10095;</button>
    </div>

    <!-- Bouton pour ouvrir la checklist -->
    <button class="checklist-btn" onclick="openModal()">Voir la Checklist</button>
    <button class="checklist-btn" onclick="location.href='accusation.php?partie=<?php echo $id_partie; ?>&joueur=<?php echo $numero_joueur; ?>'">Accusation</button>
    <button id="hypothesisButton" class="checklist-btn" onclick="openHypothesisModal()">
    Faire une hypothèse
</button>

<!-- Formulaire pour faire une hypothèse -->
<div id="hypothesisModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Formuler une hypothèse</h2>
        <form id="hypothesisForm" method="post" action="javascript:void(0);">
            <label for="prof">Personnage :</label>
            <select name="prof" id="prof">
                <option value="8">M. Nahide</option>
                <option value="9">Mme. Yakari</option>
            </select>
            <br>

            <label for="salle">Salle :</label>
            <select name="salle" id="salle">
                <option value="15">Salle jaune</option>
                <option value="16">Salle rouge</option>
                
            </select>
            <br>

            <label for="matiere">Matière :</label>
            <select name="matiere" id="matiere">
                <option value="1">Base de données</option>
                <option value="2">PHP</option>
                <option value="3">Réseau</option>
                <option value="4">Marketing</option>
            </select>
            <br>

            <input type="hidden" name="id_joueur" value="<?php echo $id_joueur; ?>">
            <input type="hidden" name="id_partie" value="<?php echo $id_partie; ?>">

            <button type="submit" onclick="submitHypothesis()">Valider l'hypothèse</button>
        </form>
        <button class="close-btn" onclick="closeHypothesisModal()">Fermer</button>
    </div>
</div>

<!-- Zone pour afficher la réponse -->
<div id="hypothesis-response"></div>
  <!-- Fenêtre modale -->
  <div id="checklistModal" class="modal">
    <div class="modal-content">
      <h2>Checklist</h2>
      <table>
        <thead>
          <tr>
            <th></th>
            <!-- Colonnes générées dynamiquement -->
            <?php 
            for ($i = 1; $i <= $nb_joueurs; $i++) {
                echo "<th>Joueur $i</th>";
            }
            ?>
          </tr>
        </thead>
        <tbody>
          <!-- Section WHO -->
          <tr><th>Qui ?</th></tr>
          <tr>
            <td>M.Nahide</td>
            <?php 
            for ($i = 1; $i <= $nb_joueurs; $i++) {
                echo "<td onclick='toggleCell(this)'></td>"; // Ajout de la fonction onclick pour chaque cellule
            }
            ?>
          </tr>
          <tr>
            <td>Mme.Yakari</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Mme.Maxout</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>M.Windouze</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>M.Pioupiou</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>M.Ami</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>M.Le galet</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>

          <!-- Section WHAT -->
          <tr><th>Quelle matière ?</th></tr>
          <tr>
            <td>BDD</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>PHP</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Réseau</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Marketing</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Mathématique</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>HTML / CSS</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Anglais</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>

          <!-- Section WHERE -->
          <tr><th>Où ?</th></tr>
          <tr>
            <td>Salle verte</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Salle Rouge</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Salle Jaune</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Salle Bleu</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Salle Magenta</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Salle Gris</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
            <td>Salle Orange</td>
            <?php for ($i = 1; $i <= $nb_joueurs; $i++) { echo "<td onclick='toggleCell(this)'></td>"; } ?>
          </tr>
          <tr>
        </tbody>
      </table>

      <!-- Bouton pour fermer la fenêtre modale -->
      <button class="close-btn" onclick="closeModal()">Fermer</button>
    </div>
  </div>

<script>

    // Fonction pour vérifier dynamiquement si le joueur est dans une pièce
    function checkDansPieceStatus() {
        const partieId = <?php echo $id_partie; ?>;
        const joueurId = <?php echo $numero_joueur; ?>;

        // Appel à check_dans_piece.php pour récupérer la valeur de dans_piece
        fetch(`check_dans_piece.php?partie=${partieId}&joueur=${joueurId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const hypothesisButton = document.querySelector('.checklist-btn:nth-of-type(3)');
                    if (data.dans_piece === 1) {
                        hypothesisButton.disabled = false; // Activer le bouton
                    } else {
                        hypothesisButton.disabled = true;  // Désactiver le bouton
                    }
                }
            })
            .catch(error => console.error('Erreur lors de la vérification de dans_piece:', error));
    }

    // Initialisation au chargement de la page
    window.onload = function () {
        // Vérification régulière toutes les 2 secondes
        setInterval(checkDansPieceStatus, 2000);

        // Initialiser le carrousel si des images sont disponibles
        if (images.length > 0) {
            showImage(currentIndex);
        } else {
            document.getElementById('carousel-image').alt = "Aucune carte disponible";
        }
    };

    const images = <?php echo json_encode($images); ?>;
    let currentIndex = 0;

    function showImage(index) {
        const imageElement = document.getElementById('carousel-image');
        imageElement.src = images[index];
    }

    function prevImage() {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
        showImage(currentIndex);
    }

    function nextImage() {
        currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
        showImage(currentIndex);
    }

    function openModal() {
        document.getElementById("checklistModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("checklistModal").style.display = "none";
    }

    function openHypothesisModal() {
    document.getElementById('hypothesisModal').style.display = 'block';
}

// Fonction pour cacher le formulaire
function closeHypothesisModal() {
    document.getElementById('hypothesisModal').style.display = 'none';
}

// Fonction pour soumettre l'hypothèse via AJAX
function submitHypothesis() {
    const form = document.getElementById('hypothesisForm');
    const formData = new FormData(form);

    // Envoyer les données via AJAX
    fetch('traiter_hypothese.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        // Afficher la réponse dans la zone dédiée
        document.getElementById('hypothesis-response').innerHTML = result;

        // Cacher le formulaire après la validation
        closeHypothesisModal();
    })
    .catch(error => {
        console.error('Erreur lors de la soumission de l\'hypothèse:', error);
    });
}

// Fonction pour alterner entre "?", "X", et vide
function toggleCell(cell) {
    // Vérifier l'état actuel et changer l'état en fonction du cycle "vide" -> "?" -> "X"
    if (cell.classList.contains('vide') || !cell.classList.length) {
        cell.classList.remove('vide');
        cell.classList.add('interrogation');
        cell.innerHTML = "?";
    } else if (cell.classList.contains('interrogation')) {
        cell.classList.remove('interrogation');
        cell.classList.add('croix');
        cell.innerHTML = "X";
    } else if (cell.classList.contains('croix')) {
        cell.classList.remove('croix');
        cell.classList.add('vide');
        cell.innerHTML = "";  // Mettre la cellule à vide
    }
}
  </script>
</body>
</html>
