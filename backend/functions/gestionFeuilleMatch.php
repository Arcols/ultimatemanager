<?php
// Récupère les joueurs actifs et leur rôle dans un match
function getJoueursActifsEtRole($linkpdo, $idMatch) {
    $stmt = $linkpdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance FROM joueur WHERE Statut = 'Actif'");
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($players as &$player) {
        $stmtCheck = $linkpdo->prepare("SELECT Poste, Role FROM participer WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
        $stmtCheck->execute([':idJoueur' => $player['Id_joueur'], ':idMatch' => $idMatch]);
        $assigned = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        $player['assigned'] = $assigned ? true : false; // Marque si le joueur est assigné à ce match
        $player['poste'] = $assigned['Poste'] ?? ''; // Récupère le poste, sinon vide
        $player['role'] = $assigned['Role'] ?? ''; // Récupère le rôle, sinon vide
    }

    return $players;
}

// Récupère les informations d'un match par son ID
function getMatch($linkpdo, $idMatch){
    $stmt = $linkpdo->prepare("SELECT * FROM rencontre WHERE Id_Match = :id");
    $stmt->execute([':id' => $idMatch]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Insère les joueurs dans la feuille de match après vérification des titulaires
function insertFeuilleMatch($linkpdo, $idMatch, $joueursMatch) {
    $titulairesCount = 0;
    foreach ($joueursMatch as $joueur) {
        if ($joueur['role'] === 'Titulaire') {
            $titulairesCount++; // Compte les titulaires
        }
    }

    if ($titulairesCount === 7) { // Vérifie qu'il y a bien 7 titulaires
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
        return true; // Insère les joueurs dans la feuille de match
    } else {
        return false; // Échec si le nombre de titulaires n'est pas correct
    }
}

// Met à jour la date du match si elle est future
function updateDateMatch($linkpdo, $idMatch, $nouvelleDate){
    if(new DateTime($nouvelleDate) < new DateTime()){ // Vérifie si la date est passée
        return false;
    }
    $stmt = $linkpdo->prepare("UPDATE rencontre SET Date_Heure = :nouvelleDate WHERE Id_Match = :idMatch");
    $stmt->execute([
        ':nouvelleDate' => $nouvelleDate,
        ':idMatch' => intval($idMatch),
    ]);
    return true; // Met à jour la date du match
}

// Valide le format de la date
function validateDateFormat($date) {
    $d = DateTime::createFromFormat('Y-m-d\TH:i', $date); // Vérifie le format ISO 8601
    return $d && $d->format('Y-m-d\TH:i') === $date;
}

// Récupère les participants d'un match
function getParticipants($linkpdo,$idMatch){
    $stmtParticipants = $linkpdo->prepare("SELECT J.Id_joueur, J.Nom, J.Prénom, P.Poste, P.Role AS Role, P.Note 
                                            FROM joueur J, participer P
                                            WHERE J.Id_joueur = P.Id_joueur 
                                            AND P.Id_Match = :idMatch");
    $stmtParticipants->execute([':idMatch' => $idMatch]);
    return $stmtParticipants->fetchAll(PDO::FETCH_ASSOC);
}

// Met à jour le score du match
function updateScore($linkpdo, $idMatch, $score){
    $stmt = $linkpdo->prepare("UPDATE rencontre SET Résultat = :score WHERE Id_Match = :idMatch");
    $stmt->execute([
        ':score' => $score,
        ':idMatch' => intval($idMatch),
    ]);
    return true; // Met à jour le score
}

// Supprime un match si sa date n'est pas passée
function deleteMatch($linkpdo, $idMatch){
    $match = getMatch($linkpdo, $idMatch);
    if (new DateTime($match['Date_Heure']) < new DateTime()) {
        return false; // Empêche la suppression si la date du match est passée
    }

    $stmt = $linkpdo->prepare("DELETE FROM participer WHERE Id_Match = :idMatch");
    $stmt->execute([':idMatch' => intval($idMatch)]);

    $stmt = $linkpdo->prepare("DELETE FROM rencontre WHERE Id_Match = :idMatch");
    $stmt->execute([':idMatch' => intval($idMatch)]);

    return true; // Supprime le match
}

// Met à jour les notes des joueurs après le match
function updateNotes($linkpdo, $idMatch, $joueursModifNote){
    $match = getMatch($linkpdo, $idMatch);
    if (new DateTime($match['Date_Heure']) > new DateTime()) {
        return false; // Empêche la modification si la date du match est future
    }
    $stmt = $linkpdo->prepare("UPDATE participer SET Note = :note WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
    foreach ($joueursModifNote as $joueur) {
        $stmt->execute([
            ':note' => intval($joueur['note']),
            ':idJoueur' => intval($joueur['id']),
            ':idMatch' => intval($idMatch),
        ]);
    }
    return true; // Met à jour les notes des joueurs
}
?>
