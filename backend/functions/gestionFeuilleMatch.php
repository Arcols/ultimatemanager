<?php
function getJoueursActifsEtRole($linkpdo, $idMatch) {
    $stmt = $linkpdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance FROM joueur WHERE Statut = 'Actif'");
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($players as &$player) {
        $stmtCheck = $linkpdo->prepare("SELECT Poste, Role FROM participer WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
        $stmtCheck->execute([':idJoueur' => $player['Id_joueur'], ':idMatch' => $idMatch]);
        $assigned = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        $player['assigned'] = $assigned ? true : false;
        $player['poste'] = $assigned['Poste'] ?? '';
        $player['role'] = $assigned['Role'] ?? '';
    }

    return $players;
}

function getMatch($linkpdo, $idMatch){
    $stmt = $linkpdo->prepare("SELECT * FROM rencontre WHERE Id_Match = :id");
    $stmt->execute([':id' => $idMatch]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertFeuilleMatch($linkpdo, $idMatch, $joueursMatch) {

    $titulairesCount = 0;
    foreach ($joueursMatch as $joueur) {
        if ($joueur['role'] === 'Titulaire') {
            $titulairesCount++;
        }
    }

    if ($titulairesCount === 7) {
        $stmtDelete = $linkpdo->prepare("DELETE FROM participer WHERE Id_Match = :idMatch");
        $stmtDelete->execute([':idMatch' => $idMatch]);

        $stmtInsert = $linkpdo->prepare("INSERT INTO participer (Id_joueur, Id_Match, Poste, Role) VALUES (:idJoueur, :idMatch, :poste, :role)");
        foreach ($joueursMatch as $joueur) {
            $stmtInsert->execute([
                ':idJoueur' => $joueur['id'],
                ':idMatch' => $idMatch,
                ':poste' => $joueur['poste'],
                ':role' => $joueur['role']
            ]);
        }
        return true;
    } else {
        return false;
    }
}

function updateDateMatch($linkpdo, $idMatch, $nouvelleDate){
    if(new DateTime($nouvelleDate) < new DateTime()){
        return false;
    }
    $stmt = $linkpdo->prepare("UPDATE rencontre SET Date_Heure = :nouvelleDate WHERE Id_Match = :idMatch");
    $stmt->execute([
        ':nouvelleDate' => $nouvelleDate,
        ':idMatch' => intval($idMatch),
    ]);
    return true;
}

/**
 * La date doit etre au format 'Y-m-dTH:i' au format ISO 8601
 * @param $nouvelleDate
 * @return bool
 */
function validateDateFormat($date) {
    $d = DateTime::createFromFormat('Y-m-d\TH:i', $date);
    return $d && $d->format('Y-m-d\TH:i') === $date;
}

function getParticipants($linkpdo,$idMatch){
    // Récupération des participants
    $stmtParticipants = $linkpdo->prepare("SELECT J.Id_joueur, J.Nom, J.Prénom, P.Poste, P.Role AS Role, P.Note 
                                                FROM joueur J, participer P
                                                WHERE J.Id_joueur = P.Id_joueur 
                                                AND P.Id_Match = :idMatch");
    $stmtParticipants->execute([':idMatch' => $idMatch]);
    return $stmtParticipants->fetchAll(PDO::FETCH_ASSOC);
}

function updateScore($linkpdo, $idMatch, $score){
    $stmt = $linkpdo->prepare("UPDATE rencontre SET Résultat = :score WHERE Id_Match = :idMatch");
    $stmt->execute([
        ':score' => $score,
        ':idMatch' => intval($idMatch),
    ]);
    return true;
}

function deleteMatch($linkpdo, $idMatch){
    $match = getMatch($linkpdo, $idMatch);
    if (new DateTime($match['Date_Heure']) < new DateTime()) {
        return false;
    }

    $stmt = $linkpdo->prepare("DELETE FROM participer WHERE Id_Match = :idMatch");
    $stmt->execute([':idMatch' => intval($idMatch)]);

    $stmt = $linkpdo->prepare("DELETE FROM rencontre WHERE Id_Match = :idMatch");
    $stmt->execute([':idMatch' => intval($idMatch)]);

    return true;
}

function updateNotes($linkpdo, $idMatch, $joueursModifNote){
    $match = getMatch($linkpdo, $idMatch);
    if (new DateTime($match['Date_Heure']) > new DateTime()) {
        return false;
    }
    $stmt = $linkpdo->prepare("UPDATE participer SET Note = :note WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
    foreach ($joueursModifNote as $joueur) {
        $stmt->execute([
            ':note' => intval($joueur['note']),
            ':idJoueur' => intval($joueur['id']),
            ':idMatch' => intval($idMatch),
        ]);
    }
    return true;
}
?>