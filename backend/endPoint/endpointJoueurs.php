<?php

require_once '../functions/connection_bd.php';
require_once '../functions/gestionJoueurs.php';
require_once '../functions/function.php';
require_once '../functions/validate_token.php';

if(!getBearerToken()){
    deliver_response(401, "Vous n'avez pas fourni de token");
    exit;
}

if(!validate_token(getBearerToken())) {
    deliver_response(401, "Vous n'avez pas un token valide");
    exit;
}
$linkpdo = connectionToDB();

if (is_string($linkpdo)) {
    deliver_response(500, $linkpdo);
    exit;
}

$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {
    case 'GET':
        $joueurs = getJoueurs($linkpdo);
        deliver_response(200, "ok", $joueurs);
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            deliver_response(400, "Invalid JSON");
            exit;
        }

        $numLic = $input['numLic'] ?? null;
        $nom = $input['nom'] ?? null;
        $prenom = $input['prénom'] ?? null;
        $date_naissance = $input['date_naissance'] ?? null;
        $taille = $input['taille'] ?? null;
        $poid = $input['poid'] ?? null;
        $commentaire = $input['commentaire'] ?? "Pas de commentaire";
        $statut = $input['statut'] ?? null;

        if ($numLic && $nom && $prenom && $date_naissance && $taille && $poid && $statut) {
            insertJoueur($linkpdo, $numLic, $nom, $prenom, $date_naissance, $taille, $poid, $commentaire, $statut);
            deliver_response(201, "Player created");
        } else {
            deliver_response(402, "Missing parameters");
        }
        break;
    default:
        deliver_response(405, "Method Not Allowed");
        break;
}
?>