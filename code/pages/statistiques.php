<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}

function getMatchsStats($pdo) {
    $stmt = $pdo->query("SELECT 
SUM(CASE WHEN résultat LIKE '%:%' AND CAST(SUBSTRING_INDEX(résultat, ':', 1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(résultat, ':', -1) AS UNSIGNED) THEN 1 ELSE 0 END) AS gagnés,
                            SUM(CASE WHEN résultat LIKE '%:%' AND CAST(SUBSTRING_INDEX(résultat, ':', 1) AS UNSIGNED) = CAST(SUBSTRING_INDEX(résultat, ':', -1) AS UNSIGNED) THEN 1 ELSE 0 END) AS nuls,
SUM(CASE WHEN résultat LIKE '%:%' AND CAST(SUBSTRING_INDEX(résultat, ':', 1) AS UNSIGNED) < CAST(SUBSTRING_INDEX(résultat, ':', -1) AS UNSIGNED) THEN 1 ELSE 0 END) AS perdus
                         FROM rencontre");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPostePréféré($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT Poste, COUNT(*) as Occurrence 
                           FROM participer 
                           WHERE id_joueur = :id 
                           GROUP BY Poste 
                           ORDER BY Occurrence DESC 
                           LIMIT 1");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn();
}

function getNombreDeTitularisation($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id AND Role = 0");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn();
}

function getNombreDeRemplacements($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id AND Role = 1");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn();
}

function getEvaluationMoyenne($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT AVG(Note) FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    $result = $stmt->fetchColumn();
    return $result !== null ? $result : 0;
}

function getMatchsGagnés($pdo, $idJoueur) {
    // Récupérer les id_match de la table participer pour le joueur donné
    $stmt = $pdo->prepare("SELECT id_match FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    $matches = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($matches)) {
        return 0;
    }

    // Récupérer les résultats des matchs de la table rencontre
    $placeholders = str_repeat('?,', count($matches) - 1) . '?';
    $stmt = $pdo->prepare("SELECT résultat FROM rencontre WHERE id_match IN ($placeholders)");
    $stmt->execute($matches);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $gagnés = 0;
    $total = count($results);

    foreach ($results as $result) {
        list($scoreEquipe, $scoreAdversaire) = explode(':', $result['résultat']);
        if ((int)$scoreEquipe > (int)$scoreAdversaire) {
            $gagnés++;
        }
    }

    return $total > 0 ? ($gagnés / $total) * 100 : 0;
}

function getNombreDeSelectionConsecutive($pdo, $idJoueur) {
    // Récupérer les dates des matchs joués par le joueur
    $stmt = $pdo->prepare("SELECT r.Date_Heure 
                           FROM participer p
                           JOIN rencontre r ON p.id_match = r.id_match
                           WHERE p.id_joueur = :id
                           ORDER BY r.Date_Heure ASC");
    $stmt->execute([':id' => $idJoueur]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($dates)) {
        return 0;
    }

    $maxConsecutive = 0;
    $currentConsecutive = 1;

    for ($i = 1; $i < count($dates); $i++) {
        $previousDate = new DateTime($dates[$i - 1]);
        $currentDate = new DateTime($dates[$i]);

        // Vérifier si les dates sont consécutives (différence d'un jour)
        $interval = $previousDate->diff($currentDate)->days;
        if ($interval == 1) {
            $currentConsecutive++;
        } else {
            if ($currentConsecutive > $maxConsecutive) {
                $maxConsecutive = $currentConsecutive;
            }
            $currentConsecutive = 1;
        }
    }

    // Vérifier la dernière séquence
    if ($currentConsecutive > $maxConsecutive) {
        $maxConsecutive = $currentConsecutive;
    }

    return $maxConsecutive;
}

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer les joueurs ayant participer à des matchs
    $stmt = $pdo->query("SELECT DISTINCT j.Id_joueur, j.Nom, j.Prénom, j.Statut 
                         FROM joueur j
                         JOIN participer p ON j.Id_joueur = p.id_joueur");
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $matchsStats = getMatchsStats($pdo);
    $totalMatchs = $matchsStats['gagnés'] + $matchsStats['nuls'] + $matchsStats['perdus'];
    $pourcentageGagnés = $totalMatchs > 0 ? ($matchsStats['gagnés'] / $totalMatchs) * 100 : 0;
    $pourcentageNuls = $totalMatchs > 0 ? ($matchsStats['nuls'] / $totalMatchs) * 100 : 0;
    $pourcentagePerdus = $totalMatchs > 0 ? ($matchsStats['perdus'] / $totalMatchs) * 100 : 0;
} catch (PDOException $e) {
    echo "<p>'>Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Statistiques</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/statistiques.css">
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <div class="main">
        <h1>Statistiques</h1>
        <p>Nombre total de matchs gagnés : <?= $matchsStats['gagnés'] ?></p>
        <p>Nombre total de matchs nuls : <?= $matchsStats['nuls'] ?></p>
        <p>Nombre total de matchs perdus : <?= $matchsStats['perdus'] ?></p>
        <p>Pourcentage de matchs gagnés : <?= number_format($pourcentageGagnés, 2) ?>%</p>
        <p>Pourcentage de matchs nuls : <?= number_format($pourcentageNuls, 2) ?>%</p>
        <p>Pourcentage de matchs perdus : <?= number_format($pourcentagePerdus, 2) ?>%</p>
        <table border="1" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Statut</th>
                    <th>Poste Préféré</th>
                    <th>Nombre de titularisation</th>
                    <th>Nombre de remplacements</th>
                    <th>Évaluation moyenne</th>
                    <th>Matchs gagnés (%)</th>
                    <th>Nombre de sélection consécutive</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($joueurs as $joueur): ?>
                    <tr>
                        <td><?= htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Statut'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(getPostePréféré($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(getNombreDeTitularisation($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(getNombreDeRemplacements($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format(getEvaluationMoyenne($pdo, $joueur['Id_joueur']), 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format(getMatchsGagnés($pdo, $joueur['Id_joueur']), 2), ENT_QUOTES, 'UTF-8') ?>%</td>
                        <td><?= htmlspecialchars(getNombreDeSelectionConsecutive($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>