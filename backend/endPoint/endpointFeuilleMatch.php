<?php

require_once '../functions/connection_bd.php';
require_once '../functions/gestionFeuilleMatch.php';
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
    case 'GET': // Récupère les détails d'un match et ses participants
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            if ($match = getMatch($linkpdo, $idMatch)) {
                $joueursActifs = getJoueursActifsEtRole($linkpdo, $idMatch);
                $participants = getParticipants($linkpdo, $idMatch);
                $response = [
                    'match' => $match,
                    'joueurs' => $joueursActifs,
                    'participants' => $participants
                ];
                deliver_response(200, "ok", $response);
            } else {
                deliver_response(404, "Match not found");
            }
        } else {
            deliver_response(400, "Invalid or missing ID");
        }
        break;

    case 'POST': // Crée une feuille de match avec les joueurs
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                deliver_response(400, "Invalid JSON");
                exit;
            }
            $joueursMatch = $input['joueursMatch'] ?? null;
            if (insertFeuilleMatch($linkpdo, $idMatch, $joueursMatch)) {
                deliver_response(201, "Feuille de match créée");
            } else {
                deliver_response(400, "Il faut 7 titulaires exactement");
            }
        } else {
            deliver_response(400, "Invalid or missing IDMatch");
        }
        break;

    case 'PUT': // Met à jour la date, le score ou les notes des joueurs d'un match
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                deliver_response(400, "Invalid JSON");
                exit;
            }

            // Mise à jour de la date du match
            if (isset($input['nouvelle_date'])) {
                $nouvelleDate = $input['nouvelle_date'];
                if (!$nouvelleDate || !validateDateFormat($nouvelleDate)) {
                    deliver_response(400, "Invalid date format " . $nouvelleDate);
                } elseif (updateDateMatch($linkpdo, $idMatch, $nouvelleDate)) {
                    deliver_response(200, "Date du match modifiée");
                } else {
                    deliver_response(401, "La date du match doit être dans le futur");
                }
            }

            // Mise à jour du score du match
            if (isset($input['score'])) {
                $score = $input['score'];
                if (updateScore($linkpdo, $idMatch, $score)) {
                    deliver_response(200, "Score modifié");
                } else {
                    deliver_response(401, "Impossible de modifier le score d'un match passé");
                }
            }

            // Mise à jour des notes des joueurs
            if (isset($input['joueursModifNote'])) {
                $joueursModifNote = $input['joueursModifNote'];
                if (updateNotes($linkpdo, $idMatch, $joueursModifNote)) {
                    deliver_response(200, "Notes modifiées");
                } else {
                    deliver_response(401, "Vous ne pouvez pas modifier les notes d'un match pas encore joué");
                }
            }
        } else {
            deliver_response(400, "Invalid or missing IDMatch");
        }
        break;

    case 'DELETE': // Supprime un match s'il n'est pas passé
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            if (deleteMatch($linkpdo, $idMatch)) {
                deliver_response(200, "Match supprimé");
            } else {
                deliver_response(401, "Impossible de supprimer un match passé");
            }
        } else {
            deliver_response(400, "Invalid or missing IDMatch");
        }
        break;

    default:
        deliver_response(405, "Method Not Allowed");
        break;
}

?>
