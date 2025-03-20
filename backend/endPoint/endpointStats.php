<?php

require_once '../functions/connection_bd.php';
require_once '../functions/gestionStats.php';
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
    case 'GET': // Récupération des statistiques

        // Récupère les statistiques des matchs
        $matchsStats = getMatchsStats($linkpdo);
        $totalMatchs = $matchsStats['gagnés'] + $matchsStats['nuls'] + $matchsStats['perdus'];

        // Calcul des pourcentages de victoires, nuls et défaites
        $stats = [
            "gagnés" => $matchsStats['gagnés'],
            "nuls" => $matchsStats['nuls'],
            "perdus" => $matchsStats['perdus'],
            "totalMatchs" => $totalMatchs,
            "pourcentageGagnés" => $totalMatchs > 0 ? number_format(($matchsStats['gagnés'] / $totalMatchs) * 100, 2) : 0,
            "pourcentageNuls" => $totalMatchs > 0 ? number_format(($matchsStats['nuls'] / $totalMatchs) * 100, 2) : 0,
            "pourcentagePerdus" => $totalMatchs > 0 ? number_format(($matchsStats['perdus'] / $totalMatchs) * 100, 2) : 0
        ];

        // Récupération des joueurs
        $joueurs = recupererJoueurs($linkpdo);
        $tab = [];

        // Parcours chaque joueur pour récupérer ses statistiques individuelles
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

        // Regroupe toutes les statistiques dans un tableau final
        $data = [
            "Stats" => $stats,
            "Tab" => $tab
        ];

        deliver_response(200, "ok", $data);
        break;

    default:
        deliver_response(405, "Method Not Allowed");
        break;
}

?>
