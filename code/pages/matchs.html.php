<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Matchs</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/joueur.css">
    <style>
        table tbody tr {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <div class="main">
        <?php include './../php/matchs.php'; ?>
        <h1>Gestion des Matchs</h1>

        <!-- Formulaire de filtre -->
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
            <p style='color: red;'><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <p style='color: green;'><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <table border="1" style="border-collapse: collapse; width: 100%; margin-top: 20px;">
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
            if ($rows) {
                foreach ($rows as $row) {
                    // Reformater la date et l'heure
                    $dateTime = new DateTime($row['Date_Heure']);
                    $date = $dateTime->format('d/m/Y');
                    $heure = $dateTime->format('H\hi');
                    $resultat = !empty($row['Résultat']) ? htmlspecialchars($row['Résultat']) : "- <b>:</b> -";

                    // Déterminer la page cible en fonction de la date
                    $detailsPage = $dateTime < $currentDateTime ? "details_match_apres.php" : "details_match_avant.php";

                    echo "<tr onclick=\"window.location.href='$detailsPage?id=" . $row['Id_Match'] . "'\">
                            <td>" . htmlspecialchars($date) . "</td>
                            <td>" . htmlspecialchars($heure) . "</td>
                            <td>" . htmlspecialchars($row['Lieu']) . "</td>
                            <td>" . htmlspecialchars($row['Nom_adversaire']) . "</td>
                            <td>" . $resultat . "</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Aucun match trouvé.</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <!-- Formulaire pour ajouter un match -->
        <form method="POST" action="./../php/ajout_match.php">
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
