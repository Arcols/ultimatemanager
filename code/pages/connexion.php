<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Connexion 2</title>
    <link rel="stylesheet" type="text/css" href="./../css/global.css">
    <link rel="stylesheet" type="text/css" href="./../css/connexion.css">
</head>
<body>
    <div class="login-container">
        <div>
            <img src="./../../ressources/logo-ultimatemanager.png" alt="Ultimate Manager Logo">
        </div>

        <!-- Formulaire de connexion -->
        <form method="POST" action="">
            <div>
                <img src="./../../ressources/person-fill.svg" alt="Identifiant">
                <input type="text" name="login" placeholder="Identifiant" required><br>
            </div>
            <div>
                <img src="./../../ressources/key.svg" alt="Mot de passe">
                <input type="password" name="mdp" placeholder="Mot de passe" required><br>
            </div>
            
            <button type="submit">Se connecter</button>
        </form>

        <!-- Zone pour les messages d'erreur -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Connexion à la base de données
            try {
                $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("<p>'>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>");
            }

            // Récupérer les données du formulaire
            $login = $_POST['login'];
            $mdp = $_POST['mdp'];
            $cle = "quoicoubeh";
            $mdp_hache = hash_hmac('sha256',$mdp,$cle);
            //echo "mdp haché : " . $mdp_hache;

            //// Créer un utilisateur
            //$sql = "INSERT INTO utilisateur (login, mdp) VALUES (:login, :mdp_hache)";
            //$stmt = $pdo->prepare($sql);
            //$stmt->execute([
            //    ':login' => $login,
            //    ':mdp_hache' => $mdp_hache
            //]);


            // Vérifier l'utilisateur dans la base de données
            $stmt = $pdo->prepare("SELECT mdp FROM utilisateur WHERE login = :login");
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($mdp_hache === $user['mdp']) {
                    // Connexion réussie
                    session_start();
                    $_SESSION['login'] = $login; // Stocke le login dans la session
                    header("Location: joueur.php"); // Redirige vers la page principale
                    exit;
                } else {
                    echo "<p>'>Identifiant ou mot de passe incorrect.</p>";
                }
            } else {
                echo "<p>'>Identifiant ou mot de passe incorrect.</p>";
            }
        }
        ?>
    </div>
</body>
</html>

