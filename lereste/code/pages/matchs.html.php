<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Matchs</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/matchs.css">
</head>
<body>
    <!-- Inclusion de l'en-tête du site -->
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    
    <div class="main">
        <!-- Inclusion du fichier PHP qui récupère les matchs -->
        <?php include './../php/matchs.php'; ?>

        <h1>Gestion des Matchs</h1>
        <!-- Formulaire de filtre pour afficher les matchs selon le type -->
        <form method="GET" action="">
            <label for="filtre">Afficher :</label>
            <select name="filtre" id="filtre" onchange="this.form.submit()">
                <option value="tous" <?php if ($filtre === 'tous') echo 'selected'; ?>>Tous les matchs</option>
                <option value="passes" <?php if ($filtre === 'passes') echo 'selected'; ?>>Matchs passés</option>
                <option value="avenir" <?php if ($filtre === 'avenir') echo 'selected'; ?>>Matchs à venir</option>
            </select>
        </form>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php if (!empty($errorMessage)): ?>
            <p><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- Table des matchs -->
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Lieu</th>
                    <th>Adversaires</th>
                    <th>Résultat</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Affichage des matchs
            if ($rows) {
                foreach ($rows as $row) {
                    // Reformater la date et l'heure
                    $dateTime = new DateTime($row['Date_Heure']);
                    $date = $dateTime->format('d/m/Y');
                    $heure = $dateTime->format('H\hi');
                    $resultat = !empty($row['Résultat']) ? htmlspecialchars($row['Résultat']) : "- <b>:</b> -";

                    // Déterminer la page cible en fonction de la date (avant ou après le match)
                    $detailsPage = $dateTime < $currentDateTime ? "details_apres_match.html.php" : "details_avant_match.html.php";

                    // Affichage d'une ligne pour chaque match, avec redirection vers la page de détails
                    echo "<tr onclick=\"window.location.href='$detailsPage?id=" . $row['Id_Match'] . "'\">
                            <td>" . htmlspecialchars($date) . "</td>
                            <td>" . htmlspecialchars($heure) . "</td>
                            <td>" . htmlspecialchars($row['Lieu']) . "</td>
                            <td>" . htmlspecialchars($row['Nom_adversaire']) . "</td>
                            <td>" . $resultat . "</td>
                        </tr>";
                }
            } else {
                // Affichage d'un message si aucun match n'est trouvé
                echo "<tr><td colspan='5'>Aucun match trouvé.</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <!-- Formulaire pour ajouter un match -->
        <form method="POST" action="../php/matchs.php">
            <p>
                <label for="date_heure">Date </label>
                <input type="datetime-local" id="date_heure" name="date_heure" required>
            </p>
            <p>
                <label for="nom_adversaires">Nom de l'equipe adverse </label>
                <input type="text" id="nom_adversaires" name="nom_adversaires" required>
            </p>
            <p>
                <label for="lieu">Lieu </label>
                <select id="lieu" name="lieu" required>
                    <option value="Domicile">domicile</option>
                    <option value="Exterieur">extérieur</option>
                </select>
            </p>
            <p>
                <label for="resultat1">Resultat </label>
                <input type="Number" id="resultat1" name="resultat1" min=0 max=15 step=1>
                <label for="resultat2"> : </label>
                <input type="Number" id="resultat2" name="resultat2" min=0 max=15 step=1>
            </p>
            <button type="submit">Ajouter Match</button>
        </form>
    </div>
</body>
</html>
