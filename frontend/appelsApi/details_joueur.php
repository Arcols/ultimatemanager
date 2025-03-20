<?php
session_start(); // Démarre la session pour accéder aux variables de session

$joueur = [];
$error = null;

// Calcule l'âge à partir de la date de naissance
function calculateAge($date_naissance) {
    $date_naissance = new DateTime($date_naissance); // Convertir la date de naissance en objet DateTime
    $today = new DateTime(); // Date actuelle
    return $today->diff($date_naissance)->y; // Retourne la différence en années
}

// Récupère les détails d'un joueur via une API
function getJoueurDetails($idJoueur) {
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointDetailsJoueur.php?id=' . $idJoueur; // URL de l'API

    // Initialiser cURL
    $ch = curl_init($url);

    // Définir les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_HTTPGET, true); // Utiliser la méthode GET
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer " . $_SESSION['jwt_token'] // Ajouter le token JWT pour l'authentification
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification de l'hôte SSL

    // Exécuter la requête
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Code HTTP de la réponse

    if ($result === false) { // Vérifie si une erreur cURL se produit
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Server error', 'data' => null); // Retourne une erreur serveur
    }

    curl_close($ch);

    // Vérifie si la réponse est un JSON valide
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("JSON error: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'JSON error', 'data' => null); // Retourne une erreur JSON
    }

    return array_merge(['status' => $http_code], $response); // Retourne la réponse API avec le code HTTP
}

// Met à jour les détails d'un joueur via une API
function updateJoueurDetails($data) {
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointDetailsJoueur.php'; // URL de l'API

    // Initialiser cURL
    $ch = curl_init($url);

    // Définir les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Utiliser la méthode PUT
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Données à envoyer en PUT
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer " . $_SESSION['jwt_token'] // Ajouter le token JWT pour l'authentification
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification de l'hôte SSL

    // Exécuter la requête
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Code HTTP de la réponse

    if ($result === false) { // Vérifie si une erreur cURL se produit
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Server error', 'data' => null); // Retourne une erreur serveur
    }

    curl_close($ch);

    // Vérifie si la réponse est un JSON valide
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("JSON error: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'JSON error', 'data' => null); // Retourne une erreur JSON
    }

    return array_merge(['status' => $http_code], $response); // Retourne la réponse API avec le code HTTP
}

// Supprime un joueur via une API
function deleteJoueur($idJoueur){
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointDetailsJoueur.php?id=' . $idJoueur; // URL de l'API

    // Initialiser cURL
    $ch = curl_init($url);

    // Définir les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner la réponse sous forme de chaîne
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Utiliser la méthode DELETE
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer " . $_SESSION['jwt_token'] // Ajouter le token JWT pour l'authentification
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification de l'hôte SSL

    // Exécuter la requête
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Code HTTP de la réponse

    if ($result === false) { // Vérifie si une erreur cURL se produit
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Server error', 'data' => null); // Retourne une erreur serveur
    }

    curl_close($ch);

    // Vérifie si la réponse est un JSON valide
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("JSON error: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'JSON error', 'data' => null); // Retourne une erreur JSON
    }

    return array_merge(['status' => $http_code], $response); // Retourne la réponse API avec le code HTTP
}

try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) { // Vérifie si un ID joueur valide est passé
        $idJoueur = intval($_GET['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérifie si la méthode est POST
            if(isset($_POST['delete'])) { // Si une suppression est demandée
                $response = deleteJoueur($idJoueur); // Supprimer le joueur
                if ($response['status'] == 200) {
                    header('Location: ./../pages/joueurs.html.php'); // Rediriger si la suppression réussit
                    exit;
                } else {
                    if($response['status'] == 401) {
                        header('Location: ./../pages/connexion.html.php'); // Rediriger si non autorisé
                        exit;
                    }
                    $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur"); // Afficher l'erreur
                }
            }
            // Mise à jour des détails du joueur
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
                $message = "Les informations ont été mises à jour avec succès."; // Message de succès
                header('Location: ./../pages/joueurs.html.php'); // Rediriger si la mise à jour réussit
            }else {
                if ($response['status'] == 401) {
                    header('Location: ./../pages/connexion.html.php'); // Rediriger si non autorisé
                    exit;
                }
            }
        } else {
            // Récupérer les détails du joueur
            $response = getJoueurDetails($idJoueur);
            if ($response['status'] == 200) {
                $joueur = $response['data']; // Remplir les détails du joueur
            } else {
                $error = "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Pas de message d'erreur"); // Afficher l'erreur
            }
        }
    } else {
        $error = "Identifiant de joueur invalide."; // Message si l'ID est invalide
    }
} catch (Exception $e) {
    $error = "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'); // Gérer les erreurs
}
?>
