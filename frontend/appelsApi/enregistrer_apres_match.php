<?php

session_start();
function update($data,$id) {
    $url = 'http://localhost/BUT/R3.01/ultimatemanager/backend/endpointFeuilleMatch.php?id='.$id;

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Use PUT method
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Set PUT fields
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer " . $_SESSION['jwt_token']
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL verification

    // Execute the request
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) {
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Server error', 'data' => null);
    }

    curl_close($ch);

    // Check if the response is valid JSON
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("JSON error: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'JSON error', 'data' => null);
    }

    return array_merge(['status' => $http_code], $response);
}

try {
    // Récupérer l'identifiant du match depuis le POST
    $idMatch = $_POST['id_match'] ?? null;

    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Mise à jour du score dans la table "rencontre"
    $score1 = $_POST['score1'] ?? null;
    $score2 = $_POST['score2'] ?? null;

    if (isset($score1, $score2)) {
        $resultat = $score1 . ':' . $score2;
        $data = [
            "score" => $resultat
        ];
        $response  = update($data, $idMatch);
        if ($response['status'] !== 200) {
            header("Location: ./../pages/details_apres_match.html.php?id=" . intval($idMatch). "&error=".$response['status_message']);
            exit;
        }
    }

    // Mise à jour des notes des joueurs dans la table "Participer"
    $data = [
        "joueursModifNote" => []
    ];

    foreach ($_POST['notes'] as $idJoueur => $note) {
        $data['joueursModifNote'][] = [
            "id" => intval($idJoueur),
            "note" => intval($note)
        ];
    }

    $response = update($data, $idMatch);

    if ($response['status'] !== 200) {
        header("Location: ./../pages/details_apres_match.html.php?id=" . intval($idMatch). "&error=".$response['status_message']);
        exit;
    }

    // Redirection vers la page details_apres_match.html.php
    header("Location: ./../pages/details_apres_match.html.php?id=" . intval($idMatch));
    exit;
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>
