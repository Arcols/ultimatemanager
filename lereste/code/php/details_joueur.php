<?php
session_start();
require_once 'connection_bd.php';
require_once './../../../backend/validate_token.php';
validate_token();

$joueur = [];
$error = null;

function calculateAge($date_naissance) {
    $date_naissance = new DateTime($date_naissance);
    $today = new DateTime();
    return $today->diff($date_naissance)->y;
}

function getJoueurDetails($idJoueur) {
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointDetailsJoueur.php?id=' . $idJoueur;

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true); // Use GET method
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
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

function updateJoueurDetails($data) {
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointDetailsJoueur.php';

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Use PUT method
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Set PUT fields
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
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

function deleteJoueur($idJoueur){
    $url = 'https://ultimatemanager.alwaysdata.net/backend/endpointDetailsJoueur.php?id=' . $idJoueur;

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Use DELETE method
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
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
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $idJoueur = intval($_GET['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['delete']))
            {
                $response = deleteJoueur($idJoueur);
                if ($response['status'] == 200) {
                    header('Location: ./../pages/joueurs.html.php');
                    exit;
                } else {
                    $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur");
                }
            }
            $data = [
                'id' => $idJoueur,
                'licence' => $_POST['licence'] ?? null,
                'taille' => $_POST['taille'] ?? null,
                'poid' => $_POST['poid'] ?? null,
                'commentaire' => $_POST['commentaire'] ?? null,
                'status' => $_POST['status'] ?? null
            ];

            $response = updateJoueurDetails($data);
            if ($response['status'] == 200) {
                $message = "Les informations ont été mises à jour avec succès.";
                header('Location: ./../pages/joueurs.html.php');
            }
        } else {
            $response = getJoueurDetails($idJoueur);
            if ($response['status'] == 200) {
                $joueur = $response['data'];
            } else {
                $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur");
            }
        }
    } else {
        $error = "Identifiant de joueur invalide.";
    }
} catch (Exception $e) {
    $error = "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>