<?php
session_start();

require_once 'connection_bd.php';

function getJoueursEtMatch($id) {
    $url = 'http://localhost/BUT/R3.01/ultimatemanager/backend/endpointFeuilleMatch.php?id='.$id;
    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true); // Use GET method
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

$idMatch = null;
$match = null;
$participants = [];
$errorMessage = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMatch = intval($_GET['id']);
    try {
        $pdo = connectionToDB();
        $response = getJoueursEtMatch($idMatch);
        $match = $response['data']['match'] ?? null;
        $participants = $response['data']['participants'] ?? [];

        if ($match) {
            // Vérifier si la date du match est après la date actuelle
            $dateMatch = new DateTime($match['Date_Heure']);
            $currentDate = new DateTime();

            if ($dateMatch > $currentDate) {
                header("Location: details_avant_match.html.php?id=" . $idMatch);
                exit;
            }
        } else {
            $errorMessage = "Match introuvable.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Erreur : " . htmlspecialchars($e->getMessage());
    }
} else {
    $errorMessage = "Aucun match spécifié.";
}
?>