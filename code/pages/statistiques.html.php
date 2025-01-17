<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Statistiques</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/statistiques.css">
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <div class="main">
        <h1>Statistiques</h1>
        <?php include './../php/statistiques.php'; ?>
        <p>Nombre total de matchs gagnés : <?= $matchsStats['gagnés'] ?></p>
        <p>Nombre total de matchs nuls : <?= $matchsStats['nuls'] ?></p>
        <p>Nombre total de matchs perdus : <?= $matchsStats['perdus'] ?></p>
        <p>Pourcentage de matchs gagnés : <?= number_format($pourcentageGagnés, 2) ?>%</p>
        <p>Pourcentage de matchs nuls : <?= number_format($pourcentageNuls, 2) ?>%</p>
        <p>Pourcentage de matchs perdus : <?= number_format($pourcentagePerdus, 2) ?>%</p>
        <table border="1" style="border-collapse: collapse; width: 100%;">
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
                <?php foreach ($joueurs as $joueur): ?>
                    <tr>
                        <td><?= htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Statut'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(getPostePréféré($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(getNombreDeTitularisation($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(getNombreDeRemplacements($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format(getEvaluationMoyenne($pdo, $joueur['Id_joueur']), 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format(getMatchsGagnés($pdo, $joueur['Id_joueur']), 2), ENT_QUOTES, 'UTF-8') ?>%</td>
                        <td><?= htmlspecialchars(getNombreDeSelectionConsecutive($pdo, $joueur['Id_joueur']), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>