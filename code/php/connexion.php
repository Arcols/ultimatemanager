<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connexion à la base de données
    try {
        $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("<p>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>");
    }

    // Récupérer les données du formulaire
    $login = $_POST['login'];
    $mdp = $_POST['mdp'];
    $cle = "quoicoubeh";
    $mdp_hache = hash_hmac('sha256', $mdp, $cle);

    // Vérifier l'utilisateur dans la base de données
    $stmt = $pdo->prepare("SELECT mdp FROM utilisateur WHERE login = :login");
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($mdp_hache === $user['mdp']) {
            // Connexion réussie
            session_start();
            $_SESSION['login'] = $login; // Stocke le login dans la session
            header("Location: joueur.html.php"); // Redirige vers la page principale
            exit;
        } else {
            echo "<p>Identifiant ou mot de passe incorrect.</p>";
        }
    } else {
        echo "<p>Identifiant ou mot de passe incorrect.</p>";
    }
}
?>
