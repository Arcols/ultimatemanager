<?php

require_once '../functions/connection_bd.php';
require_once '../functions/gestionDetailsJoueur.php';
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
    case 'GET': // Récupère les informations d'un joueur
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idJoueur = intval($_GET['id']);
            $joueur = getJoueur($linkpdo, $idJoueur);
            if ($joueur) {
                // Vérifie si le joueur a déjà participé à un match
                $joueur['aParticipéAUnMatch'] = aParticipéAUnMatch($linkpdo, $idJoueur);
                deliver_response(200, "ok", $joueur);
            } else {
                deliver_response(404, "Player not found");
            }
        } else {
            deliver_response(400, "Invalid or missing ID");
        }
        break;

    case 'PUT': // Met à jour les informations d'un joueur
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            deliver_response(400, "Invalid JSON");
            exit;
        }

        // Récupération des paramètres du joueur
        $idJoueur = $input['id'] ?? null;
        $licence = $input['licence'] ?? null;
        $taille = $input['taille'] ?? null;
        $poid = $input['poid'] ?? null;
        $commentaire = $input['commentaire'] ?? "Pas de commentaire";
        $status = $input['status'] ?? null;

        if ($idJoueur && $licence && $taille && $poid && $commentaire && $status) {
            updateJoueur($linkpdo, $idJoueur, $licence, $taille, $poid, $commentaire, $status);
            deliver_response(200, "Player updated");
        } else {
            deliver_response(400, "Missing parameters");
        }
        break;

    case 'DELETE': // Supprime un joueur de la base de données
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idJoueur = intval($_GET['id']);
            deleteJoueur($linkpdo, $idJoueur);
            deliver_response(200, "Player deleted");
        } else {
            deliver_response(400, "Invalid or missing ID");
        }
        break;

    default:
        deliver_response(405, "Method Not Allowed");
        break;
}

?>
