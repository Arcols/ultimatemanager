<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match</title>
    <link rel="stylesheet" href="./../css/global.css">
    <script>
        function toggleComboboxes(checkbox) {
            const row = checkbox.closest('tr');
            const posteSelect = row.querySelector('.poste');
            const roleSelect = row.querySelector('.role');
            posteSelect.style.display = checkbox.checked ? 'inline' : 'none';
            roleSelect.style.display = checkbox.checked ? 'inline' : 'none';
        }

        function confirmDelete() {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce match ? Cette action est irréversible.")) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>

    <h1>Détails du Match</h1>
    <?php include './../php/details_avant_match.php'; ?>
    <?php if (isset($errorMessage)): ?>
        <p style="color:red;"><?= $errorMessage ?></p>
    <?php else: ?>
        <?php if ($error): ?>
            <script>alert('Il doit y avoir exactement 7 titulaires sélectionnés.');</script>
        <?php endif; ?>

        <?php if ($match): ?>
            <p>Match <?= htmlspecialchars($match['Lieu'] === 'domicile' ? "à domicile" : "à l'extérieur") ?></p>
            <p>Contre <?= htmlspecialchars($match['Nom_adversaire']) ?></p>
            <p>Le <?= (new DateTime($match['Date_Heure']))->format('d/m/Y \à H\hi') ?></p>

            <form method="POST" action="./../php/enregistrer_avant_match.php">
                <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
                <label for="nouvelle_date">Nouvelle date :</label>
                <input type="datetime-local" name="nouvelle_date" id="nouvelle_date" value="<?= (new DateTime($match['Date_Heure']))->format('Y-m-d\TH:i') ?>" min="<?= date('Y-m-d\TH:i') ?>">
                <button type="submit">Modifier la date</button>
            </form>

            <h2>Joueurs ayant participé</h2>
            <form method="POST" action="./../php/enregistrer_avant_match.php">
                <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
                <table border="1" style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Licence</th>
                            <th>Âge</th>
                            <th>Taille</th>
                            <th>Poid</th>
                            <th>Commentaire</th>
                            <th>Choix</th>
                            <th>Poste</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $player): ?>
                            <tr>
                                <td><?= htmlspecialchars($player['Nom']) ?></td>
                                <td><?= htmlspecialchars($player['Prénom']) ?></td>
                                <td><?= htmlspecialchars($player['Numéro_de_licence']) ?></td>
                                <td><?= htmlspecialchars(calculateAge($player['Date_de_naissance'])) ?></td>
                                <td><?= htmlspecialchars($player['Taille']) ?></td>
                                <td><?= htmlspecialchars($player['Poid']) ?></td>
                                <td><?= htmlspecialchars($player['Commentaire'] ?? 'Pas de commentaire') ?></td>
                                <td>
                                    <input type="checkbox" name="choix_<?= htmlspecialchars($player['Id_joueur']) ?>" onclick="toggleComboboxes(this)" <?= $player['assigned'] ? 'checked' : '' ?>>
                                </td>
                                <td>
                                    <select class="poste" style="display: <?= $player['assigned'] ? 'inline' : 'none' ?>;" name="poste_<?= htmlspecialchars($player['Id_joueur']) ?>">
                                        <option value="Attaquant" <?= $player['poste'] === 'Attaquant' ? 'selected' : '' ?>>Attaquant</option>
                                        <option value="Milieu" <?= $player['poste'] === 'Milieu' ? 'selected' : '' ?>>Milieu</option>
                                        <option value="Défenseur" <?= $player['poste'] === 'Défenseur' ? 'selected' : '' ?>>Défenseur</option>
                                        <option value="Gardien" <?= $player['poste'] === 'Gardien' ? 'selected' : '' ?>>Gardien</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="role" style="display: <?= $player['assigned'] ? 'inline' : 'none' ?>;" name="role_<?= htmlspecialchars($player['Id_joueur']) ?>">
                                        <option value="Titulaire" <?= $player['role'] === 'Titulaire' ? 'selected' : '' ?>>Titulaire</option>
                                        <option value="Remplaçant" <?= $player['role'] === 'Remplaçant' ? 'selected' : '' ?>>Remplaçant</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Valider</button>
            </form>
        <?php else: ?>
            <p>Match introuvable.</p>
        <?php endif; ?>
    <?php endif; ?>

    <form id="deleteForm" method="POST" action="./../php/enregistrer_avant_match.php">
        <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
        <input type="hidden" name="action" value="supprimer">
    </form>
    <button type="button" onclick="confirmDelete()">Supprimer le match</button>
</body>
</html>
