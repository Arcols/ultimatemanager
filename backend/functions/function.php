<?php
// Fonction pour envoyer une réponse HTTP avec des données JSON
function deliver_response($status_code, $status_message, $data = null) {
    // Définit les en-têtes HTTP pour la réponse
    http_response_code($status_code); // Définit le code de statut HTTP
    header("Content-Type: application/json; charset=utf-8"); // Spécifie que la réponse est en format JSON
    header("Access-Control-Allow-Origin: *"); // Permet l'accès depuis toutes les origines (CORS)
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Définit les méthodes HTTP autorisées
    header("Access-Control-Allow-Headers: Content-Type"); // Autorise les en-têtes spécifiques

    // Crée la réponse avec le code de statut, message et données
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    // Convertit la réponse en format JSON
    $json_response = json_encode($response);
    if ($json_response === false) // Si l'encodage échoue, arrête et affiche l'erreur
        die('json encode ERROR : ' . json_last_error_msg());

    // Envoie la réponse JSON au client
    echo $json_response;
}

// Fonction pour récupérer l'en-tête Authorization dans la requête
function getAuthorizationHeader() {
    $headers = getallheaders(); // Récupère tous les en-têtes HTTP
    if (isset($headers['Authorization'])) { // Si l'en-tête Authorization existe
        return $headers['Authorization']; // Retourne l'en-tête Authorization
    }
    return null; // Sinon, retourne null
}

// Fonction pour extraire le token Bearer de l'en-tête Authorization
function getBearerToken() {
    $authHeader = getAuthorizationHeader(); // Récupère l'en-tête Authorization
    if ($authHeader) { // Si l'en-tête existe
        // Cherche le token Bearer dans l'en-tête
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $trouve)) {
            return $trouve[1]; // Retourne le token extrait
        }
    }
    return null; // Si aucun token n'est trouvé, retourne null
}
?>
