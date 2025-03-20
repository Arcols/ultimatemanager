<?php
// Récupère tous les joueurs de la base de données
function getJoueurs($pdo){
    $stmt = $pdo->query("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Statut, Date_de_naissance FROM joueur");
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne les joueurs sous forme de tableau associatif
}

// Insère un nouveau joueur dans la base de données
function insertJoueur($pdo,$numLic,$nom,$prenom,$date_naissance,$taille,$poid,$commentaire,$statut){
    $stmt = $pdo->prepare("INSERT INTO joueur (Numéro_de_licence, Nom, Prénom, Date_de_naissance, Taille, Poid, Commentaire, Statut) 
                                   VALUES (:numLic,:nom, :prenom, :date_naissance, :taille, :poid, :commentaire, :statut)");

    // Exécute l'insertion avec les données fournies
    $stmt->execute([
        ':numLic' => $numLic,
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':date_naissance' => $date_naissance,
        ':taille' => $taille,
        ':poid' => $poid,
        ':commentaire' => $commentaire,
        ':statut' => $statut
    ]);
}
?>
