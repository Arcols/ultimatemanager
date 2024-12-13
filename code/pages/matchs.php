<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Joueur</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/joueur.css">
    <style>
        /* Ajouter un style pour rendre les lignes cliquables */
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

        <!-- Formulaire de filtre avec déclenchement automatique -->
        <form method="GET" action="">
            <label for="filtre">Afficher :</label>
            <select name="filtre" id="filtre" onchange="this.form.submit()">
                <option value="tous" <?php if (!isset($_GET['filtre']) || $_GET['filtre'] === 'tous') echo 'selected'; ?>>Tous les matchs</option>
                <option value="passes" <?php if (isset($_GET['filtre']) && $_GET['filtre'] === 'passes') echo 'selected'; ?>>Matchs passés</option>
                <option value="avenir" <?php if (isset($_GET['filtre']) && $_GET['filtre'] === 'avenir') echo 'selected'; ?>>Matchs à venir</option>
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
                try {
                    // Connexion à la base de données
                    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', 'root');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Déterminer le filtre sélectionné
                    $filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'tous';

                    // Construire la requête en fonction du filtre
                    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM Rencontre";
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

                    // Générer les lignes dynamiquement
                    if ($rows) {
                        foreach ($rows as $row) {
                            // Reformater la date et l'heure
                            $dateTime = new DateTime($row['Date_Heure']);
                            $date = $dateTime->format('d/m/Y'); // Format français : jj/mm/aaaa
                            $heure = $dateTime->format('H\hi'); // Heure sans secondes : HHhmm

                            // Si le résultat est vide, afficher "- <b>:</b> -"
                            $resultat = !empty($row['Résultat']) ? htmlspecialchars($row['Résultat']) : "- <b>:</b> -";

                            // Déterminer la page cible en fonction de la date
                            $detailsPage = $dateTime < $currentDateTime ? "details_match_apres.php" : "details_match_avant.php";

                            // Générer une ligne cliquable avec un lien vers `details_match.php`
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
                } catch (PDOException $e) {
                    echo "<tr><td colspan='5' style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
