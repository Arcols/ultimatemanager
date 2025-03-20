<?php
session_start();

$joueurs = [];
$error = null;

// Fonction pour récupérer les joueurs via l'API
function getJoueurs() {
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointJoueurs.php';
    // Initialiser cURL
    $ch = curl_init($url);

    // Définir les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true); // Utiliser la méthode GET
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer " . $_SESSION['jwt_token']
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification de l'hôte SSL

    // Exécuter la requête
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) { // Vérifie s'il y a une erreur cURL
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Server error', 'data' => null);
    }

    curl_close($ch);

    // Vérifier si la réponse est un JSON valide
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("JSON error: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'JSON error', 'data' => null);
    }

    return array_merge(['status' => $http_code], $response); // Retourner les résultats
}

// Fonction pour ajouter un joueur via l'API
function postJoueur($data) {
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endPoint/endpointJoueurs.php';

    // Initialiser cURL
    $ch = curl_init($url);

    // Définir les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // Utiliser la méthode POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Ajouter les données à envoyer
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer " . $_SESSION['jwt_token']
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification de l'hôte SSL

    // Exécuter la requête
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) { // Vérifie s'il y a une erreur cURL
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Server error', 'data' => null);
    }

    curl_close($ch);

    // Vérifier si la réponse est un JSON valide
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("JSON error: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'JSON error', 'data' => null);
    }

    return array_merge(['status' => $http_code], $response); // Retourner les résultats
}

try {
    // Gérer la méthode POST pour ajouter un joueur
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données soumises via le formulaire
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

        // Appel à la fonction pour ajouter un joueur
        $response = postJoueur($data);
        if ($response['status'] == 201) { // Si le joueur a été ajouté avec succès
            header('Location: ./../pages/joueurs.html.php'); // Rediriger vers la liste des joueurs
            exit;
        } else {
            if ($response['status'] == 401) { // Vérifie si la session est expirée
                header('Location: ./../pages/connexion.html.php'); // Rediriger vers la page de connexion
                exit;
            }
            $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur"); // Afficher l'erreur
        }
    } else {
        // Récupérer les joueurs si la méthode n'est pas POST
        $response = getJoueurs();
        if ($response['status'] == 200) {
            $joueurs = $response['data']; // Remplir le tableau de joueurs
        } else {
            if ($response['status'] == 401) { // Vérifie si la session est expirée
                header('Location: ./../pages/connexion.html.php'); // Rediriger vers la page de connexion
                exit;
            }
            $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur"); // Afficher l'erreur
        }
    }

    // Fonction pour calculer l'âge d'un joueur à partir de sa date de naissance
    function calculateAge($date_naissance) {
        $date_naissance = new DateTime($date_naissance);
        $today = new DateTime();
        return $today->diff($date_naissance)->y; // Retourne l'âge en années
    }

} catch (Exception $e) {
    $error = "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'); // Gérer les exceptions
}
?>
