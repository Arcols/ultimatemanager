<?php

require_once 'connection_bd.php';
require_once 'gestionMatchs.php';
require_once 'function.php';

$linkpdo = connectionToDB();
if (is_string($linkpdo)) {
    deliver_response(500, $linkpdo);
    exit;
}

$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {
    case 'GET':
        $matchs = getAllMatches($linkpdo);
        $matchsAVenir = getMachesAVenir($linkpdo);
        $matchsPasses = getMatchesPasses($linkpdo);
        deliver_response(200, "ok", ["matchs" => $matchs, "matchsAVenir" => $matchsAVenir, "matchsPasses" => $matchsPasses]);
        break;
    case 'POST' :
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            deliver_response(400, "Invalid JSON");
            exit;
        }
        $date_heure = $input['date_heure'] ?? null;
        $nom_adversaires = $input['nom_adversaires'] ?? null;
        $lieu = $input['lieu'] ?? null;
        $resultatMonEquipe = $input['resultatMonEquipe'] ?? null;
        $resultatAdversaire = $input['resultatAdversaire'] ?? null;
        if($date_heure && $nom_adversaires && $lieu){
            if(empty($resultatMonEquipe) || empty($resultatAdversaire) && new DateTime($date_heure) < new DateTime()){
                deliver_response(400, "Vous devez renseigner les résultats pour un match qui est déjà passé");
                break;
            }
            insertMatch($linkpdo, $date_heure, $nom_adversaires, $lieu, $resultatMonEquipe, $resultatAdversaire);
            deliver_response(201, "Match created");
        }else{
            deliver_response(400, "Missing parameters");
        }
        break;
    default:
        deliver_response(405, "Method Not Allowed");
        break;
}

?>