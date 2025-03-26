<?php
require_once "function.php";

// Valide un token en envoyant une requête API via cURL
function validate_token($token) {

    $url = 'https://immolink.alwaysdata.net/authapi.php'; // URL de l'API

    // Initialiser cURL
    $ch = curl_init($url);

    // Définir les options de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_HTTPGET, true); // Utiliser la méthode GET
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json", // Indiquer le type de contenu
        "Accept: application/json", // Indiquer les types de réponse acceptés
        "Authorization: Bearer " . $token // Ajouter le token d'authentification
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification SSL pour l'hôte

    // Exécuter la requête cURL
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtenir le code HTTP de la réponse

    if ($result === false) { // Vérifier si la requête échoue
        curl_close($ch);
        return $result; // Retourner le résultat de l'erreur cURL
    }

    curl_close($ch);

    // Vérifier si la réponse est un JSON valide
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false; // Retourner false si JSON est invalide
        exit;
    }

    // Vérifier si la réponse API est correcte
    if ($http_code !== 200 || $response['status'] !== 200) {
        return false; // Retourner false si la réponse n'est pas correcte
        exit;
    }
    
    return true; // Retourner true si le token est valide
}

