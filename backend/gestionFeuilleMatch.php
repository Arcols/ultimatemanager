<?php
function getMatch($pdo, $idMatch) {
    $stmt = $pdo->prepare("SELECT Id_Match, Date_Heure, Nom_adversaire, Lieu, Résultat 
                           FROM rencontre WHERE Id_Match = :id");
    $stmt->execute([':id' => $idMatch]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function avantOuApresMatch($pdo,$idMatch) {
    require_once 'gestionJoueurs.php';
    $stmt = $pdo->prepare("SELECT Date_Heure FROM rencontre WHERE Id_Match = :id");
    $stmt->execute([':id' => $idMatch]);
    $dateTime = $stmt->fetch(PDO::FETCH_ASSOC);

    $matchDateTime = new DateTime($dateTime['Date_Heure']);
    $currentDateTime = new DateTime();
    if($matchDateTime < $currentDateTime){
        $stmt = $pdo->prepare("SELECT Id_Match, Note, Poste, participer.Role, joueur.*
                                FROM participer, joueur WHERE Id_Match = :id AND participer.Id_joueur= joueur.Id_joueur");
        $stmt->execute([':id' => $idMatch]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else{
        return getJoueurs($pdo);
    }
}

function deleteMatch($pdo, $idMatch) {
    $stmt = $pdo->prepare("DELETE FROM rencontre WHERE Id_Match = :id");
    $stmt->execute([':id' => $idMatch]);
}

function updateMatch($pdo, $idMatch, $Date_Heure, $Nom_adversaire, $Lieu, $Résultat) {
    $updateStmt = $pdo->prepare("UPDATE rencontre SET Date_Heure = :Date_Heure, Nom_adversaire = :Nom_adversaire, Lieu = :Lieu, Résultat = :Résultat WHERE Id_Match = :id");
    $updateStmt->execute([
        ':Date_Heure' => $Date_Heure,
        ':Nom_adversaire' => $Nom_adversaire,
        ':Lieu' => $Lieu,
        ':Résultat' => $Résultat,
        ':id' => $idMatch
    ]);
}

?>