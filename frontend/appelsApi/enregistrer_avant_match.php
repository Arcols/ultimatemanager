<?php
session_start();
function recupererJoueursSelectionnes() {
    $joueursMatch = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'choix_') === 0) {
            $idJoueur = substr($key, 6);
            $poste = $_POST['poste_' . $idJoueur] ?? '';
            $role = $_POST['role_' . $idJoueur] ?? '';
            $joueursMatch[] = [
                'id' => intval($idJoueur),
                'poste' => $poste,
                'role' => $role,
                "Authorization: Bearer " . $_SESSION['jwt_token']
            ];
        }
    }
    return ['joueursMatch' => $joueursMatch];
}

function updateDate($data,$id) {

    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointFeuilleMatch.php?id='.$id;

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

function deleteMatch($id){
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointFeuilleMatch.php?id='.$id;

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Use DELETE method
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

function updateFeuilleMatchWithPlayer($data,$id){
    $url = 'https://backend-ultimate-manager.alwaysdata.net/backend/endPoint/endpointFeuilleMatch.php?id='.$id;

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Use POST method
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
    // Récupérer l'identifiant du match depuis le POST
    $idMatch = $_POST['id_match'] ?? null;
    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Si l'action est de supprimer le match
    if (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        $response=deleteMatch($idMatch);
        if ($response['status'] === 200) {
            header("Location: ./../pages/matchs.html.php");
            exit;
        } else {
            header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch) . "&error=".$response['status_message']);
            exit;
        }
    }

    // Modifier la date du match si elle est fournie
    if (!empty($_POST['nouvelle_date'])) {
        $nouvelleDate = $_POST['nouvelle_date'];
        $data = [
            'nouvelle_date' => $nouvelleDate
        ];
        $response = updateDate($data,$idMatch);
        if ($response['status'] === 200) {
            header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch));
            exit;
        } else {
            header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch) . "&error=".$response['status_message']);
            exit;
        }
    }

    $joueursSelectionnes = recupererJoueursSelectionnes();
    $response = updateFeuilleMatchWithPlayer($joueursSelectionnes, $idMatch);
    if ($response['status'] === 200) {
        header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch));
        exit;
    } else {
        header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch) . "&error=".$response['status_message']);
        exit;
    }
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
