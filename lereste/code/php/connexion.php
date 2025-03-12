<?php
function login($login, $mdp) {
    $url = 'https://immolink.alwaysdata.net/authapi.php';
    $data = array('login' => $login, 'mdp' => $mdp);

    // Initialisation de cURL
    $ch = curl_init($url);

    // Configuration des options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL verification

    // Exécution de la requête
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) {
        $curl_error = curl_error($ch);
        print("cURL error: " . $curl_error);
        curl_close($ch);
        return array('status' => 500, 'status_message' => 'Erreur serveur', 'data' => null);
    }

    curl_close($ch);

    // Vérifier si la réponse est bien du JSON
    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        print("Erreur JSON: " . json_last_error_msg());
        return array('status' => 500, 'status_message' => 'Erreur JSON', 'data' => null);
    }

    return array_merge(['status' => $http_code], $response);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $login = $_POST['login'] ?? null;
    $mdp = $_POST['mdp'] ?? null;

    if (!$login || !$mdp) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    $response = login($login, $mdp);

    if ($response['status'] == 200 && isset($response['data']['token'])) {
        $_SESSION['jwt_token'] = $response['data']['token'];
        header('Location: ./../pages/joueurs.html.php');
        exit;
    } else {
        echo "Erreur  " . $response['status'] . " : " . ($response['status_message'] ?? "Inconnue");
    }
}
?>