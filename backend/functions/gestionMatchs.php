<?php
// Récupère tous les matchs, triés par date croissante
function getAllMatches($pdo) {
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre ORDER BY Date_Heure ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les matchs
}

// Récupère les matchs passés (avant la date actuelle)
function getMatchesPasses($pdo) {
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre WHERE Date_Heure < NOW() ORDER BY Date_Heure ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne les matchs passés
}

// Récupère les matchs à venir (après la date actuelle)
function getMachesAVenir($pdo) {
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre WHERE Date_Heure >= NOW() ORDER BY Date_Heure ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne les matchs à venir
}

// Insère un nouveau match dans la base de données si les conditions sont remplies
function insertMatch($pdo, $date_heure, $nom_adversaires, $lieu, $resultatMonEquipe,$resultatAdversaire) {
    if (!empty($resultatMonEquipe) && !empty($resultatAdversaire) && is_numeric($resultatMonEquipe) && is_numeric($resultatAdversaire) && new DateTime($date_heure) < new DateTime() ) {
        $resultat = $resultatMonEquipe . " : " . $resultatAdversaire; // Formate le résultat si les scores sont valides
    } elseif (new DateTime($date_heure) > new DateTime()) {
        $resultat = null; // Si la date du match est dans le futur, résultat non défini
    }else {
        return false; // Retourne false si les conditions ne sont pas remplies
    }
    $stmt = $pdo->prepare("INSERT INTO rencontre (Date_heure, Nom_adversaire, Lieu, Résultat) 
                                   VALUES (:date_heure, :nom_adversaires, :lieu, :resultat)");

    return $stmt->execute([ // Exécute l'insertion du match
        ':date_heure' => $date_heure,
        ':nom_adversaires' => $nom_adversaires,
        ':lieu' => $lieu,
        ':resultat' => $resultat,
    ]);
}
?>
