<?php
function getJoueurs($pdo){
    $stmt = $pdo->query("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Statut, Date_de_naissance FROM joueur");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertJoueur($pdo,$numLic,$nom,$prenom,$date_naissance,$taille,$poid,$commentaire,$statut){
    $stmt = $pdo->prepare("INSERT INTO joueur (Numéro_de_licence, Nom, Prénom, Date_de_naissance, Taille, Poid, Commentaire, Statut) 
                                   VALUES (:numLic,:nom, :prenom, :date_naissance, :taille, :poid, :commentaire, :statut)");

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

function deliver_response($status_code, $status_message, $data = null) {
    /// Paramétrage de l'entête HTTP
    http_response_code($status_code); // Utilise un message standardisé en fonction du code HTTP
    header("Content-Type: application/json; charset=utf-8"); // Indique au client le format de la réponse
    header("Access-Control-Allow-Origin: *"); // Autorise toutes les origines (CORS)
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Autorise les méthodes HTTP
    header("Access-Control-Allow-Headers: Content-Type"); // Autorise les en-têtes spécifiques

    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if ($json_response === false)
        die('json encode ERROR : ' . json_last_error_msg());

    /// Affichage de la réponse (retourné au client)
    echo $json_response;
}
?>