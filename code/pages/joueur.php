<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Joueur</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/joueur.css">
</head>

<body>
    <div class="header">
        <?php include './../headfoot/header.html';?>
    </div>
    <div class = "main">
        <h1>Gestion des Joueurs</h1>
        <form>
            <label for="nom">Nom du Joueur :</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="equipe">Équipe :</label>
            <input type="text" id="equipe" name="equipe" required>

            <button type="submit">Ajouter Joueur</button>
        </form>
    </div>
</body>
</html>
