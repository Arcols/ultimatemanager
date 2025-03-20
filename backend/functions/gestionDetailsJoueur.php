<?php
function deleteJoueur($pdo, $idJoueur) {
    $stmt = $pdo->prepare("DELETE FROM joueur WHERE Id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
}

function updateJoueur($pdo, $idJoueur, $licence, $taille, $poid, $commentaire, $status) {
    $updateStmt = $pdo->prepare("UPDATE joueur SET Numéro_de_licence = :licence, Taille = :taille, Poid = :poid, Commentaire = :commentaire, Statut = :status WHERE Id_joueur = :id");
    $updateStmt->execute([
        ':licence' => $licence,
        ':taille' => $taille,
        ':poid' => $poid,
        ':commentaire' => $commentaire,
        ':status' => $status,
        ':id' => $idJoueur
    ]);
}

function getJoueur($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance, Statut 
                           FROM joueur WHERE Id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function aParticipéAUnMatch($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn() > 0;
}
?>