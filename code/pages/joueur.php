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
</head>

<body>
    <div class="header">
        <?php include './../headfoot/header.html';?>
    </div>
    <div class = "main">
        <h1>Gestion des Joueurs</h1>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Licence</th>
                    <th>Age</th>
                    <th>Taille (m)</th>
                    <th>Poid (kg)</th>
                    <th>Commentaire</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    // Connexion à la base de données
                    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Requête pour récupérer les matchs
                    $stmt = $pdo->query("SELECT Numéro_de_licence, Nom, Prénom, Taille,Poid,Commentaire,Statut,Date_de_naissance FROM joueur");

                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    function calculateAge($date_naissance) {
                        $date_naissance = new DateTime($date_naissance);
                        $aujourdhui = new DateTime();
                        $age = $aujourdhui->diff($date_naissance)->y; // Différence en années
                        return $age;
                    }   
                    // Générer les lignes dynamiquement
                    if ($rows) {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['Nom']) . "</td>
                                    <td>" . htmlspecialchars($row['Prénom']) . "</td>
                                    <td>" . htmlspecialchars($row['Numéro_de_licence']) . "</td>
                                    <td>" . htmlspecialchars(calculateAge($row['Date_de_naissance'])) . "</td>
                                    <td>" . htmlspecialchars($row['Taille']) . "</td>
                                    <td>" . htmlspecialchars($row['Poid']) . "</td>
                                    <td>" . htmlspecialchars($row['Commentaire'] ?? 'Pas de commentaire') . "</td>
                                    <td>" . htmlspecialchars($row['Statut']) . "</td>
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
        <h2>Ajouter un joueur</h2>
        <form method="POST" action="./../php/ajout_joueur.php">
            <p>
                <label for="nom">Nom du Joueur </label>
                <input type="text" id="nom" name="nom" required>
            </p>
            <p>
                <label for="date_naissance">Date de naissance </label>
                <input type="date" id="date_naissance" name="date_naissance" required>
            </p>
            <p>
                <label for="prénom">Prénom du Joueur </label>
                <input type="text" id="prénom" name="prénom" required>
            </p>
            <p>
                <label for="taille">Taille (cm) </label>
                <input type="number" id="taille" name="taille" required>
            </p>
            <p>
                <label for="numLic">Numéro de licence </label>
                <input type="text" id="numLic" name="numLic" required>
            </p>
            <p>
                <label for="poid">Poids (kg) </label>
                <input type="number" id="poid" name="poid" required>
            <p>
                <label for="commentaire">Commentaire </label>
                <textarea id="commentaire" name="commentaire"></textarea>
            </p>
            <p>
                <label for="statut">Statut </label>
                <select id="statut" name="statut" required>
                    <option value="Actif">Actif</option>
                    <option value="Blessé">Blessé</option>
                    <option value="Suspendu">Suspendu</option>
                    <option value="Absent">Absent</option>
                </select>
            </p>
            <button type="submit">Ajouter Joueur</button>
    </form>
    </div>
</body>
</html>
