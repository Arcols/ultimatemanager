<?php
require_once 'jwt_utils.php';
require_once 'connection_bd.php';
require_once 'function.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['login']) || !isset($input['mdp'])) {
    deliver_response(400, 'Paramètres login et mdp requis.');
    exit;
}

$login = $input['login'];
$mdp = $input['mdp'];
$cle = "quoicoubeh";
$mdp_hache = hash_hmac('sha256', $mdp, $cle);

try {
    switch ($method) {
        case 'POST':
            $pdo = connectionToDB();

            if (!check_user_exists($pdo, $login)) {
                deliver_response(404, 'Utilisateur non trouvé.');
                exit;
            }

            $stmt = $pdo->prepare("SELECT mdp FROM utilisateurs WHERE login = :login");
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || $mdp_hache !== $user['mdp']) {
                deliver_response(401, 'Identifiant ou mot de passe incorrect.');
                exit;
            }

            // Utilisateur reconnu, générer le jeton JWT
            $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
            $payload = ['login' => $login, 'exp' => (time() + 3600)];
            $secret = 'coucou_je_suis_secret';

            $jwt = generate_jwt($headers, $payload, $secret);

            // Envoyer le jeton JWT
            deliver_response(200, 'OK', ['token' => $jwt]);
            break;

        case 'PUT':
            $pdo = connectionToDB();

            if (check_user_exists($pdo, $login)) {
                deliver_response(409, 'Utilisateur déjà existant.');
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO utilisateurs (login, mdp) VALUES (:login, :mdp)");
            $stmt->execute([':login' => $login, ':mdp' => $mdp_hache]);

            deliver_response(201, 'Utilisateur créé avec succès.');
            break;

        default:
            deliver_response(405, 'Méthode non autorisée.');
            break;
    }
} catch (Exception $e) {
    error_log("Erreur serveur: " . $e->getMessage());
    deliver_response(500, 'Erreur serveur.');
}
?>