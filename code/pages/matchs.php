<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Joueur</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/joueur.css">
</head>

<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <div class="main">
        <h1>Gestion des Matchs</h1>
        <table border="1" style="border-collapse: collapse; width: 100%;">
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
                    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Requête pour récupérer les matchs
                    $stmt = $pdo->query("SELECT Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre");
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Générer les lignes dynamiquement
                    if ($rows) {
                        foreach ($rows as $row) {
                            // Reformater la date et l'heure
                            $dateTime = new DateTime($row['Date_Heure']);
                            $date = $dateTime->format('d/m/Y'); // Format français : jj/mm/aaaa
                            $heure = $dateTime->format('H\hi'); // Heure sans secondes : HHhmm

                            // Si le résultat est vide, afficher "- <b>:</b> -"
                            $resultat = !empty($row['Résultat']) ? htmlspecialchars($row['Résultat']) : "- <b>:</b> -";

                            echo "<tr>
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
            </tbody>
        </table>
    </div>
</body>
</html>
