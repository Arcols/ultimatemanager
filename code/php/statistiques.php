<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.html.php");
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
    $stmt = $pdo->prepare("SELECT id_match FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    $matches = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($matches)) {
        return 0;
    }

    $placeholders = str_repeat('?,', count($matches) - 1) . '?';
    $stmt = $pdo->prepare("SELECT résultat FROM rencontre WHERE id_match IN ($placeholders)");
    $stmt->execute($matches);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $gagnés = 0;
    foreach ($results as $result) {
        list($scoreEquipe, $scoreAdversaire) = explode(':', $result['résultat']);
        if ((int)$scoreEquipe > (int)$scoreAdversaire) {
            $gagnés++;
        }
    }

    return count($results) > 0 ? ($gagnés / count($results)) * 100 : 0;
}

function getNombreDeSelectionConsecutive($pdo, $idJoueur) {
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

    return max($maxConsecutive, $currentConsecutive);
}

try {

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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