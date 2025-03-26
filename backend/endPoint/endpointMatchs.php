<?php

require_once '../functions/connection_bd.php';
require_once '../functions/gestionMatchs.php';
require_once '../functions/function.php';
require_once '../functions/validate_token.php';

// Vérifie la présence et la validité du token JWT
if (!getBearerToken()) {
    deliver_response(401, "Vous n'avez pas fourni de token");
    exit;
}

if (!validate_token(getBearerToken())) {
    deliver_response(401, "Vous n'avez pas un token valide");
    exit;
}

// Connexion à la base de données
$linkpdo = connectionToDB();
if (is_string($linkpdo)) {
    deliver_response(500, $linkpdo);
    exit;
}

$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method) {
    case 'GET': // Récupère la liste des matchs
        $matchs = getAllMatches($linkpdo);
        $matchsAVenir = getMachesAVenir($linkpdo);
        $matchsPasses = getMatchesPasses($linkpdo);
        deliver_response(200, "ok", [
            "matchs" => $matchs, 
            "matchsAVenir" => $matchsAVenir, 
            "matchsPasses" => $matchsPasses
        ]);
        break;

    case 'POST': // Ajoute un match à la base de données
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            deliver_response(400, "Invalid JSON");
            exit;
        }

        // Récupération et validation des données reçues
        $date_heure = $input['date_heure'] ?? null;
        $nom_adversaires = $input['nom_adversaires'] ?? null;
        $lieu = $input['lieu'] ?? null;
        $resultatMonEquipe = $input['resultatMonEquipe'] ?? null;
        $resultatAdversaire = $input['resultatAdversaire'] ?? null;

        // Vérifie que les champs obligatoires sont présents
        if ($date_heure && $nom_adversaires && $lieu) {
            // Si le match est passé, les résultats doivent être renseignés
            if (empty($resultatMonEquipe) || empty($resultatAdversaire)) {
                if (new DateTime($date_heure) < new DateTime()) {
                    deliver_response(400, "Vous devez renseigner les résultats pour un match qui est déjà passé");
                }
            }
            insertMatch($linkpdo, $date_heure, $nom_adversaires, $lieu, $resultatMonEquipe, $resultatAdversaire);
            deliver_response(201, "Match created");
        } else {
            deliver_response(400, "Missing parameters");
        }
        break;

    default:
        deliver_response(405, "Method Not Allowed");
        break;
}


