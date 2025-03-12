<?php

require_once 'connection_bd.php';
require_once 'gestionJoueurs.php';

$linkpdo = connectionToDB();

if (is_string($linkpdo)) {
    // If the connection fails, display the error message
    header('Content-Type: application/json');
    echo json_encode(['error' => $linkpdo]);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow all origins (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Allow HTTP methods
header('Access-Control-Allow-Headers: Content-Type'); // Allow specific headers

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
            deliver_response(400, "Missing parameters");
        }
        break;
    default:
        deliver_response(405, "Method Not Allowed");
        break;
}
?>