<?php

require_once 'connection_bd.php';
require_once 'gestionDetailsJoueur.php';

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
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idJoueur = intval($_GET['id']);
            $joueur = getJoueur($linkpdo, $idJoueur);
            if ($joueur) {
                $joueur['aParticipéAUnMatch'] = aParticipéAUnMatch($linkpdo, $idJoueur);
                deliver_response(200, "ok", $joueur);
            } else {
                deliver_response(404, "Player not found");
            }
        } else {
            deliver_response(400, "Invalid or missing ID");
        }
        break;
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            deliver_response(400, "Invalid JSON");
            exit;
        }

        $idJoueur = $input['id'] ?? null;
        $licence = $input['licence'] ?? null;
        $taille = $input['taille'] ?? null;
        $poid = $input['poid'] ?? null;
        $commentaire = $input['commentaire'] ?? null;
        $status = $input['status'] ?? null;

        if ($idJoueur && $licence && $taille && $poid && $commentaire && $status) {
            updateJoueur($linkpdo, $idJoueur, $licence, $taille, $poid, $commentaire, $status);
            deliver_response(200, "Player updated");
        } else {
            deliver_response(400, "Missing parameters");
        }
        break;
    case 'DELETE':
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

function deliver_response($status, $status_message, $data = null) {
    header("HTTP/1.1 $status $status_message");
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    echo json_encode($response);
}
?>