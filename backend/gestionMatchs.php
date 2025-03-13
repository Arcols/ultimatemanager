<?php
function getAllMatches($pdo) {
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre ORDER BY Date_Heure ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

function getMatchesPasses($pdo) {
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre WHERE Date_Heure < NOW() ORDER BY Date_Heure ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

function getMachesAVenir($pdo) {
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre WHERE Date_Heure >= NOW() ORDER BY Date_Heure ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertMatch($pdo, $date_heure, $nom_adversaires, $lieu, $resultatMonEquipe,$resultatAdversaire) {
    if (!empty($resultatMonEquipe) && !empty($resultatAdversaire) && is_numeric($resultatMonEquipe) && is_numeric($resultatAdversaire) && new DateTime($date_heure) < new DateTime() ) {
        $resultat = $resultatMonEquipe . " : " . $resultatAdversaire;
    } elseif (new DateTime($date_heure) > new DateTime()) {
        $resultat = null;
    }else {
        return false;
    }
    $stmt = $pdo->prepare("INSERT INTO rencontre (Date_heure, Nom_adversaire, Lieu, Résultat) 
                                   VALUES (:date_heure, :nom_adversaires, :lieu, :resultat)");

    return $stmt->execute([
        ':date_heure' => $date_heure,
        ':nom_adversaires' => $nom_adversaires,
        ':lieu' => $lieu,
        ':resultat' => $resultat,

    ]);
}

?>