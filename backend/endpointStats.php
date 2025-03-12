<?php

require_once 'connection_bd.php';
require_once 'gestionStats.php';

$linkpdo = connectionToDB();

if (is_string($linkpdo)) {
    // Si la connexion échoue, afficher le message d'erreur
    header('Content-Type: application/json');
    echo json_encode(['error' => $linkpdo]);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Autorise toutes les origines (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Autorise les méthodes HTTP
header('Access-Control-Allow-Headers: Content-Type'); // Autorise les en-têtes spécifiques

$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method) {
    case 'GET':

        $matchsStats = getMatchsStats($linkpdo);
        $totalMatchs = $matchsStats['gagnés'] + $matchsStats['nuls'] + $matchsStats['perdus'];

        $stats = [
            "gagnés" => $matchsStats['gagnés'],
            "nuls" => $matchsStats['nuls'],
            "perdus" => $matchsStats['perdus'],
            "totalMatchs" => $totalMatchs,
            "pourcentageGagnés" => $totalMatchs > 0 ? number_format(($matchsStats['gagnés'] / $totalMatchs) * 100,2) : 0,
            "pourcentageNuls" => $totalMatchs > 0 ?  ($matchsStats['nuls'] / $totalMatchs) * 100 : 0,
            "pourcentagePerdus" => $totalMatchs > 0 ?  number_format(($matchsStats['perdus'] / $totalMatchs) * 100,2): 0
        ];

        $joueurs = recupererJoueurs($linkpdo);

        $tab = [];
        foreach ($joueurs as $joueur) {
            $tab[] = [
                "Nom" => htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8'),
                "Prénom" => htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8'),
                "Statut" => htmlspecialchars($joueur['Statut'], ENT_QUOTES, 'UTF-8'),
                "PostePréféré" => htmlspecialchars(getPostePréféré($linkpdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8'),
                "Titularisations" => htmlspecialchars(getNombreDeTitularisation($linkpdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8'),
                "Remplacements" => htmlspecialchars(getNombreDeRemplacements($linkpdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8'),
                "Evaluation" => htmlspecialchars(number_format(getEvaluationMoyenne($linkpdo, $joueur['Id_joueur']), 2), ENT_QUOTES, 'UTF-8'),
                "MatchsGagnés" => htmlspecialchars(number_format(getMatchsGagnés($linkpdo, $joueur['Id_joueur']), 2), ENT_QUOTES, 'UTF-8') . "%",
                "SelectionConsecutive" => htmlspecialchars(getNombreDeSelectionConsecutive($linkpdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8')
            ];
        }

        // Préparer le tableau final de données
        $data = [
            "Stats" => $stats,
            "Tab" => $tab
        ];

        deliver_response(200, "ok", $data);
        break;
    default:
        break;
}
?>