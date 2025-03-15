<?php
require_once 'connection_bd.php';
session_start();

$joueurs = [];
$error = null;
function getJoueurs() {
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointJoueurs.php';
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

function postJoueur($data) {
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointJoueurs.php';

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // Use POST method
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Set POST fields
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'numLic' => $_POST['numLic'] ?? null,
            'nom' => $_POST['nom'] ?? null,
            'prénom' => $_POST['prénom'] ?? null,
            'date_naissance' => $_POST['date_naissance'] ?? null,
            'taille' => $_POST['taille'] ?? null,
            'poid' => $_POST['poid'] ?? null,
            'commentaire' => $_POST['commentaire'] ?? null,
            'statut' => $_POST['statut'] ?? null
        ];

        $response = postJoueur($data);
        if ($response['status'] == 201) {
            header('Location: ./../pages/joueurs.html.php');
            exit;
        } else {
            if($response['status'] == 401) {
                header('Location: ./../pages/connexion.html.php');
                exit;
            }
            $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur");
        }
    } else {
        $response = getJoueurs();
        if ($response['status'] == 200) {
            $joueurs = $response['data'];
        } else {
            if($response['status'] == 401) {
                header('Location: ./../pages/connexion.html.php');
                exit;
            }
            $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur");
        }
    }

    // Function to calculate age
    function calculateAge($date_naissance) {
        $date_naissance = new DateTime($date_naissance);
        $today = new DateTime();
        return $today->diff($date_naissance)->y;
    }

} catch (Exception $e) {
    $error = "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>