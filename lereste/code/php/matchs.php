<?php
require_once 'connection_bd.php';

session_start();

require_once './../../../backend/validate_token.php';
function getMatches(){
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointMatchs.php';

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

function addMatch($date_heure,$nom_adversaires,$lieu,$resultatMonEquipe,$resultatEquipeAdverse){
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointMatchs.php';

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // Use POST method
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'date_heure' => $date_heure,
        'nom_adversaires' => $nom_adversaires,
        'lieu' => $lieu,
        'resultatMonEquipe' => $resultatMonEquipe,
        'resultatAdversaire' => $resultatEquipeAdverse
    ]));
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
    $filtre = isset($_GET['filtre']) ?? 'tous';
    $http_request=$_SERVER['REQUEST_METHOD'];
    switch ($http_request) {
        case 'GET' :
            $matches=getMatches();
            $matchs = [];
            if($matches['status']==200){
                $matchs=$matches['data']['matchs'];
                $matchsAVenir=$matches['data']['matchsAVenir'];
                $matchsPasses=$matches['data']['matchsPasses'];
                if ($filtre === 'passes') {
                    $matchs=$matchsPasses;
                } elseif ($filtre === 'avenir') {
                    $matchs=$matchsAVenir;
                }
            }else{
                $errorMessage=$matches['status_message'];
            }
            $rows = $matchs;
            break;

        case 'POST' :
            if (isset($_POST['date_heure'], $_POST['nom_adversaires'], $_POST['lieu'])) {
                // Récupérer les données du formulaire
                $date_heure = $_POST['date_heure'];
                $nom_adversaires = htmlspecialchars($_POST['nom_adversaires']);
                $lieu = htmlspecialchars($_POST['lieu']);
                $resulatMonEquipe = htmlspecialchars($_POST['resultat1']);
                $resultatEquipeAdverse = htmlspecialchars($_POST['resultat2']);
                $response = addMatch($date_heure,$nom_adversaires,$lieu,$resulatMonEquipe,$resultatEquipeAdverse);
                if($response['status']==201){
                    header('Location: ./../pages/matchs.html.php?success=1');
                    exit;
                }else{
                    $errorMessage=$response['status_message'];
                    header('Location: ./../pages/matchs.html.php?error='.$errorMessage);
                }
            }
    }

    // Définir l'heure actuelle
    $currentDateTime = new DateTime();
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}

// Variables de message d'erreur ou de succès
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

?>
