<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Connexion</title>
    <link rel="stylesheet" type="text/css" href="./../css/global.css">
    <link rel="stylesheet" type="text/css" href="./../css/connexion.css">
</head>
<body>
    <div class="login-container">
        <div>
            <img src="../ressources/logo-ultimatemanager.png" alt="Ultimate Manager Logo">
        </div>

        <!-- Formulaire de connexion -->
        <form method="POST" action="">
            <div>
                <img src="../ressources/person-fill.svg" alt="Identifiant">
                <input type="text" name="login" placeholder="Identifiant" required><br>
            </div>
            <div>
                <img src="../ressources/key.svg" alt="Mot de passe">
                <input type="password" name="mdp" placeholder="Mot de passe" required><br>
            </div>
            
            <button type="submit">Se connecter</button>
        </form>

        <!-- Zone pour les messages d'erreur -->
        <?php include __DIR__ . '/../appelsApi/connexion.php'; ?>
    </div>
</body>
</html>
