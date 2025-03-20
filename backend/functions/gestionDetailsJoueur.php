<?php
// Supprime un joueur par son ID
function deleteJoueur($pdo, $idJoueur) {
    $stmt = $pdo->prepare("DELETE FROM joueur WHERE Id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
}

// Met à jour les informations d'un joueur
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

// Récupère les informations d'un joueur par son ID
function getJoueur($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance, Statut FROM joueur WHERE Id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Vérifie si un joueur a participé à un match
function aParticipéAUnMatch($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn() > 0;
}
?>
