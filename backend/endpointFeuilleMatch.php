<?php

require_once 'connection_bd.php';
require_once 'gestionFeuilleMatch.php';
require_once 'function.php';
require_once 'validate_token.php';

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
switch($http_method){
    case 'GET' :
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            if($match = getMatch($linkpdo, $idMatch)){
                $joueursActifs = getJoueursActifsEtRole($linkpdo,$idMatch);
                $participants = getParticipants($linkpdo, $idMatch);
                $response = [
                    'match' => $match,
                    'joueurs' => $joueursActifs,
                    'participants' => $participants
                ];
                deliver_response(200, "ok", $response);
            }else{
                deliver_response(404, "Match not found");
            }
        }else {
            deliver_response(400, "Invalid or missing ID");
        }
        break;
    case 'POST' :
        if(isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                deliver_response(400, "Invalid JSON");
                exit;
            }
            $joueursMatch = $input['joueursMatch'] ?? null;
            if(insertFeuilleMatch($linkpdo, $idMatch, $joueursMatch)){
                deliver_response(201, "Feuille de match créée");
            }else{
                deliver_response(401, "Il faut 7 titulaires exactement");
            }
        }else{
            deliver_response(400, "Invalid or missing IDMatch");
        }
        break;
    case 'PUT' :
        if(isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                deliver_response(400, "Invalid JSON");
                exit;
            }
            // mise à jour de la date
            if(isset($input['nouvelle_date'])){
                $nouvelleDate = $input['nouvelle_date'];
                if (!$nouvelleDate || !validateDateFormat($nouvelleDate)) {
                    deliver_response(400, "Invalid date format ".$nouvelleDate);
                } elseif(updateDateMatch($linkpdo, $idMatch, $nouvelleDate)){
                    deliver_response(200, "Date du match modifiée");
                }else{
                    deliver_response(401, "La date du match doit être dans le futur");
                }
            }
            // mise à jour du score
            if(isset($input['score'])){
                $score = $input['score'];
                if(updateScore($linkpdo, $idMatch, $score)){
                    deliver_response(200, "Score modifié");
                }else{
                    deliver_response(401, "Impossible de modifier le score d'un match passé");
                }
            }
            if(isset($input['joueursModifNote'])){
                $joueursModifNote = $input['joueursModifNote'];
                if(updateNotes($linkpdo, $idMatch, $joueursModifNote)){
                    deliver_response(200, "Notes modifiées");
                }else{
                    deliver_response(401, "Vous ne pouvez pas modifier les notes d'un match pas encore joué");
                }
            }
        }else{
            deliver_response(400, "Invalid or missing IDMatch");
        }
        break;
    case 'DELETE' :
        if(isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            if(deleteMatch($linkpdo, $idMatch)){
                deliver_response(200, "Match supprimé");
            }else{
                deliver_response(401, "Impossible de supprimer un match passé");
            }
        }else{
            deliver_response(400, "Invalid or missing IDMatch");
        }
        break;
    default:
        deliver_response(405, "Method Not Allowed");
        break;
}
?>