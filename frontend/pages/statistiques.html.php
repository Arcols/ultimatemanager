<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Statistiques</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/statistiques.css">
</head>
<body>
<div class="header">
    <?php include './../headfoot/header.html'; ?>
</div>

<div class="main">
    <h1>Statistiques</h1>

    <!-- Inclusion du fichier PHP qui récupère les statistiques des matchs -->
    <?php include './../appelsAPI/statistiques.php'; ?>

    <!-- Affichage des statistiques générales -->
    <p>Nombre total de matchs gagnés : <?= $matchsStats['gagnés'] ?></p>
    <p>Nombre total de matchs nuls : <?= $matchsStats['nuls'] ?></p>
    <p>Nombre total de matchs perdus : <?= $matchsStats['perdus'] ?></p>

    <!-- Calcul et affichage des pourcentages de chaque type de match -->
    <p>Pourcentage de matchs gagnés : <?= $pourcentageGagnés ?>%</p>
    <p>Pourcentage de matchs nuls : <?= $pourcentageNuls ?>%</p>
    <p>Pourcentage de matchs perdus : <?= $pourcentagePerdus ?>%</p>

    <!-- Table des joueurs avec leurs statistiques individuelles -->
    <table>
        <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Statut</th>
            <th>Poste Préféré</th>
            <th>Nombre de titularisation</th>
            <th>Nombre de remplacements</th>
            <th>Évaluation moyenne</th>
            <th>Matchs gagnés (%)</th>
            <th>Nombre de sélection consécutive</th>
        </tr>
        </thead>
        <tbody>
        <!-- Affichage des données de chaque joueur -->
        <?php foreach ($joueurs as $joueur): ?>
            <tr>
                <td><?= $joueur['Nom'] ?></td>
                <td><?= $joueur['Prénom'] ?></td>
                <td><?= $joueur['Statut'] ?></td>
                <td><?= $joueur['PostePréféré'] ?></td>
                <td><?= $joueur['Titularisations'] ?></td>
                <td><?= $joueur['Remplacements'] ?></td>
                <td><?= $joueur['Evaluation'] ?></td>
                <td><?= $joueur['MatchsGagnés'] ?></td>
                <td><?= $joueur['SelectionConsecutive'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>