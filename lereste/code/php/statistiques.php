<?php
function getStats() {
    $url = 'http://localhost/BUT/R3.01/ultimatemanager/backEnd/endpointStats.php';

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

// Example usage
$response = getStats();
if ($response['status'] == 200) {
    $data = $response['data'];
    $matchsStats = $data['Stats'];
    $joueurs = $data['Tab'];
    $totalMatchs = $matchsStats['totalMatchs'];
    $pourcentageGagnés = $matchsStats['pourcentageGagnés'];
    $pourcentageNuls = $matchsStats['pourcentageNuls'];
    $pourcentagePerdus = $matchsStats['pourcentagePerdus'];
} else {
    echo "Error " . $response['status'] . ": " . ($response['status_message'] ?? "Unknown");
}
?>