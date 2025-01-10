<?php
session_start();

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}

try {
    $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Déterminer le filtre sélectionné
    $filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'tous';

    // Construire la requête en fonction du filtre
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre";
    if ($filtre === 'passes') {
        $query .= " WHERE Date_Heure < NOW()";
    } elseif ($filtre === 'avenir') {
        $query .= " WHERE Date_Heure >= NOW()";
    }
    $query .= " ORDER BY Date_Heure ASC"; // Tri par date croissante

    // Exécuter la requête
    $stmt = $pdo->query($query);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Définir l'heure actuelle
    $currentDateTime = new DateTime();
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}

if (isset($_GET['error'])) {
    echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";
}

// Afficher un message de succès si présent dans l'URL
if (isset($_GET['success'])) {
    echo "<p style='color: green;'>Match ajouté avec succès !</p>";
}
?>
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
