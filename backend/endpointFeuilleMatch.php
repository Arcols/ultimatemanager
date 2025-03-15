<?php

require_once 'connection_bd.php';
require_once 'gestionFeuilleMatch.php';
require_once 'function.php';

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
            $idMatch = intval($_GET['id']);
            $match = getMatch($linkpdo, $idMatch);
            if ($match) {
                $match['avantOuApresMatch'] = avantOuApresMatch($linkpdo, $idMatch);
                deliver_response(200, "ok", $match);
            } else {
                deliver_response(404, "Match not found");
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

        $idMatch = intval($_GET['id']);
        $Date_Heure = $input['Date_Heure'] ?? null;
        $Nom_adversaire = $input['Nom_adversaire'] ?? null;
        $Lieu = $input['Lieu'] ?? null;
        $Résultat = $input['Résultat'] ?? null;

        if ($idMatch && $Date_Heure && $Nom_adversaire && $Lieu && $Résultat) {
            updateMatch($linkpdo, $idMatch, $Date_Heure, $Nom_adversaire, $Lieu, $Résultat);
            deliver_response(200, "Match updated");
        } else {
            deliver_response(400, "Missing parameters");
        }
        break;
    case 'DELETE':
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idMatch = intval($_GET['id']);
            deleteMatch($linkpdo, $idMatch);
            deliver_response(200, "Match deleted");
        } else {
            deliver_response(400, "Invalid or missing ID");
        }
        break;
    default:
        deliver_response(405, "Method Not Allowed");
        break;
}
    

?>