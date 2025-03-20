<?php
// Récupère les statistiques globales des matchs (gagnés, nuls, perdus)
function getMatchsStats($pdo) {
    $stmt = $pdo->query("SELECT 
        SUM(CASE WHEN résultat LIKE '%:%' AND CAST(SUBSTRING_INDEX(résultat, ':', 1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(résultat, ':', -1) AS UNSIGNED) THEN 1 ELSE 0 END) AS gagnés,
        SUM(CASE WHEN résultat LIKE '%:%' AND CAST(SUBSTRING_INDEX(résultat, ':', 1) AS UNSIGNED) = CAST(SUBSTRING_INDEX(résultat, ':', -1) AS UNSIGNED) THEN 1 ELSE 0 END) AS nuls,
        SUM(CASE WHEN résultat LIKE '%:%' AND CAST(SUBSTRING_INDEX(résultat, ':', 1) AS UNSIGNED) < CAST(SUBSTRING_INDEX(résultat, ':', -1) AS UNSIGNED) THEN 1 ELSE 0 END) AS perdus
    FROM rencontre");
    return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne les statistiques
}

// Récupère le poste préféré d'un joueur en fonction de ses participations
function getPostePréféré($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT Poste, COUNT(*) as Occurrence 
                           FROM participer 
                           WHERE id_joueur = :id 
                           GROUP BY Poste 
                           ORDER BY Occurrence DESC 
                           LIMIT 1");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn(); // Retourne le poste le plus fréquent
}

// Compte le nombre de titularisations d'un joueur
function getNombreDeTitularisation($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id AND Role = 0");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn(); // Retourne le nombre de titularisations
}

// Compte le nombre de remplacements d'un joueur
function getNombreDeRemplacements($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id AND Role = 1");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn(); // Retourne le nombre de remplacements
}

// Calcule la note moyenne d'un joueur
function getEvaluationMoyenne($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT AVG(Note) FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    $result = $stmt->fetchColumn();
    return $result !== null ? $result : 0; // Retourne la note moyenne, ou 0 si aucune note
}

// Récupère la liste des joueurs avec leur statut
function recupererJoueurs($pdo){
    try {
        $stmt = $pdo->query("SELECT Id_joueur, Nom, Prénom, Statut FROM joueur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les joueurs
    } catch (PDOException $e) {
        return []; // Retourne un tableau vide en cas d'erreur
    }
}

// Calcule le pourcentage de matchs gagnés par un joueur
function getMatchsGagnés($pdo, $idJoueur) {
    // Récupère les matchs du joueur
    $stmt = $pdo->prepare("SELECT id_match FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    $matches = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($matches)) {
        return 0; // Aucun match trouvé
    }

    // Vérifie les résultats des matchs
    $placeholders = str_repeat('?,', count($matches) - 1) . '?';
    $stmt = $pdo->prepare("SELECT résultat FROM rencontre WHERE id_match IN ($placeholders)");
    $stmt->execute($matches);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcule le nombre de matchs gagnés
    $gagnés = 0;
    foreach ($results as $result) {
        if ($result['résultat'] !== null) {
            list($scoreEquipe, $scoreAdversaire) = explode(':', $result['résultat']);
            if ((int)$scoreEquipe > (int)$scoreAdversaire) {
                $gagnés++;
            }
        }
    }

    return count($results) > 0 ? ($gagnés / count($results)) * 100 : 0; // Retourne le pourcentage de victoires
}

// Calcule le plus grand nombre de sélections consécutives d'un joueur
function getNombreDeSelectionConsecutive($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT r.Date_Heure 
                           FROM participer p
                           JOIN rencontre r ON p.id_match = r.id_match
                           WHERE p.id_joueur = :id
                           ORDER BY r.Date_Heure ASC");
    $stmt->execute([':id' => $idJoueur]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($dates)) {
        return 0; // Aucun match trouvé
    }

    $maxConsecutive = 0;
    $currentConsecutive = 1;

    // Parcourt les dates pour calculer les sélections consécutives
    for ($i = 1; $i < count($dates); $i++) {
        $previousDate = new DateTime($dates[$i - 1]);
        $currentDate = new DateTime($dates[$i]);
        $interval = $previousDate->diff($currentDate)->days;
        if ($interval == 1) { // Si les dates sont consécutives
            $currentConsecutive++;
        } else {
            $maxConsecutive = max($maxConsecutive, $currentConsecutive);
            $currentConsecutive = 1;
        }
    }

    return max($maxConsecutive, $currentConsecutive); // Retourne la plus grande série consécutive
}
?>
