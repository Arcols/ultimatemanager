<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}


// Vérifier si un message d'erreur est passé dans l'URL
if (isset($_GET['error']) && $_GET['error'] === 'titulaires') {
    echo "
    <script type='text/javascript'>
        alert('Il doit y avoir exactement 7 titulaires sélectionnés.');
    </script>
    ";
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMatch = intval($_GET['id']);

    try {
        // Connexion à la base de données
        $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour récupérer les informations du match
        $stmt = $pdo->prepare("SELECT * FROM rencontre WHERE Id_Match = :id");
        $stmt->execute([':id' => $idMatch]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match) {
            // Vérifier si la date du match est après la date actuelle
            $dateMatch = new DateTime($match['Date_Heure']);
            $currentDate = new DateTime();
            
            // Si la date du match est après la date actuelle, rediriger vers la page "detail_match_apres.php"
            if ($dateMatch < $currentDate) {
                header("Location: details_match_apres.php?id=" . $idMatch);
                exit;
            }
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match</title>
    <link rel="stylesheet" href="./../css/global.css">
    <script>
        function toggleComboboxes(checkbox) {
            const row = checkbox.closest('tr');
            const posteSelect = row.querySelector('.poste');
            const roleSelect = row.querySelector('.role');
            if (checkbox.checked) {
                posteSelect.style.display = 'inline';
                roleSelect.style.display = 'inline';
            } else {
                posteSelect.style.display = 'none';
                roleSelect.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <h1>Match</h1>
    <?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $idMatch = intval($_GET['id']);

        try {
            // Connexion à la base de données
            $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Requête pour récupérer les informations du match
            $stmt = $pdo->prepare("SELECT * FROM rencontre WHERE Id_Match = :id");
            $stmt->execute([':id' => $idMatch]);
            $match = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($match) {
                $dateTime = new DateTime($match['Date_Heure']);
                $dateHeure = $dateTime->format('d/m/Y \à H\hi');
                $typeMatch = $match['Lieu'] === 'domicile' ? "à domicile" : "à l'extérieur";

                echo "<p>Match " . htmlspecialchars($typeMatch) . "</p>";
                echo "<p>Contre " . htmlspecialchars($match['Nom_adversaire']) . "</p>";
                echo "<p>Le $dateHeure</p>";

                // Formulaire pour modifier la date du match
                echo "<form method='POST' action='./../php/enregistrer_joueurs_match.php'>";
                echo "<input type='hidden' name='id_match' value='" . htmlspecialchars($idMatch) . "'>";
                echo "<label for='nouvelle_date'>Nouvelle date :</label>";
                echo "<input type='datetime-local' name='nouvelle_date' id='nouvelle_date' value='" . $dateTime->format('Y-m-d\TH:i') . "' min='" . date('Y-m-d\TH:i') . "'>";
                echo "<button type='submit'>Modifier la date</button>";
                echo "</form>";
            } else {
                echo "<p>Match introuvable.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>Identifiant de match invalide.</p>";
    }
    ?>

    <h2>Joueurs ayant participé</h2>
    <form method="POST" action="./../php/enregistrer_joueurs_match.php">
        <input type="hidden" name="id_match" value="<?php echo htmlspecialchars($idMatch); ?>">
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Licence</th>
                    <th>Âge</th>
                    <th>Taille</th>
                    <th>Poid</th>
                    <th>Commentaire</th>
                    <th>Choix</th>
                    <th>Poste</th>
                    <th>Rôle</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Récupération des joueurs actifs
            $stmt = $pdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance FROM joueur WHERE Statut = 'Actif'");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcul de l'âge à partir de la date de naissance
            function calculateAge($date_naissance) {
                $date_naissance = new DateTime($date_naissance);
                $aujourdhui = new DateTime();
                return $aujourdhui->diff($date_naissance)->y;
            }

            // Générer les lignes du tableau
            if ($rows) {
                foreach ($rows as $row) {
                    $idJoueur = $row['Id_joueur'];

                    // Vérifier si ce joueur est assigné pour ce match
                    $stmtCheck = $pdo->prepare("SELECT Poste, Role FROM participer WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
                    $stmtCheck->execute([':idJoueur' => $idJoueur, ':idMatch' => $idMatch]);
                    $assigned = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                    // Définir les variables pour l'affichage
                    $isAssigned = $assigned !== false;
                    $poste = $isAssigned ? $assigned['Poste'] : '';
                    $role = $isAssigned ? $assigned['Role'] : '';

                    // Générer la ligne HTML
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Nom']) . "</td>
                            <td>" . htmlspecialchars($row['Prénom']) . "</td>
                            <td>" . htmlspecialchars($row['Numéro_de_licence']) . "</td>
                            <td>" . htmlspecialchars(calculateAge($row['Date_de_naissance'])) . "</td>
                            <td>" . htmlspecialchars($row['Taille']) . "</td>
                            <td>" . htmlspecialchars($row['Poid']) . "</td>
                            <td>" . htmlspecialchars($row['Commentaire'] ?? 'Pas de commentaire') . "</td>
                            <td>
                                <input type='checkbox' name='choix_" . htmlspecialchars($idJoueur) . "' onclick='toggleComboboxes(this)' " . ($isAssigned ? 'checked' : '') . ">
                            </td>
                            <td>
                                <select class='poste' style='display: " . ($isAssigned ? 'inline' : 'none') . ";' name='poste_" . htmlspecialchars($idJoueur) . "'>
                                    <option value='Attaquant' " . ($poste === 'Attaquant' ? 'selected' : '') . ">Attaquant</option>
                                    <option value='Milieu' " . ($poste === 'Milieu' ? 'selected' : '') . ">Milieu</option>
                                    <option value='Défenseur' " . ($poste === 'Défenseur' ? 'selected' : '') . ">Défenseur</option>
                                    <option value='Gardien' " . ($poste === 'Gardien' ? 'selected' : '') . ">Gardien</option>
                                </select>
                            </td>
                            <td>
                                <select class='role' style='display: " . ($isAssigned ? 'inline' : 'none') . ";' name='role_" . htmlspecialchars($idJoueur) . "'>
                                    <option value='Titulaire' " . ($role === 'Titulaire' ? 'selected' : '') . ">Titulaire</option>
                                    <option value='Remplaçant' " . ($role === 'Remplaçant' ? 'selected' : '') . ">Remplaçant</option>
                                </select>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='10'>Aucun joueur actif trouvé.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <button type="submit">Valider</button>
    </form>
    <!-- Formulaire caché pour la suppression -->
    <form id="deleteForm" method="POST" action="./../php/enregistrer_joueurs_match.php">
        <input type="hidden" name="id_match" value="<?php echo htmlspecialchars($idMatch); ?>">
        <input type="hidden" name="action" value="supprimer">
    </form>

    <!-- Bouton de suppression avec confirmation JavaScript -->
    <button type="button" onclick="confirmDelete()">Supprimer</button>

    <script>
        function confirmDelete() {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce match ? Cette action est irréversible.")) {
                // Si l'utilisateur confirme, soumettre le formulaire
                document.getElementById('deleteForm').submit();
            }
        }
    </script>

</body>
</html>
