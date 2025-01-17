<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match</title>
    <link rel="stylesheet" href="./../css/global.css">

    <script>
        // Fonction pour afficher/masquer les comboboxes (Poste et Rôle) en fonction du checkbox
        function toggleComboboxes(checkbox) {
            const row = checkbox.closest('tr'); // Récupère la ligne du tableau associée
            const posteSelect = row.querySelector('.poste'); // Sélecteur pour le champ "Poste"
            const roleSelect = row.querySelector('.role');  // Sélecteur pour le champ "Rôle"
            posteSelect.style.display = checkbox.checked ? 'inline' : 'none';
            roleSelect.style.display = checkbox.checked ? 'inline' : 'none';
        }

        // Fonction pour confirmer la suppression d'un match
        function confirmDelete() {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce match ? Cette action est irréversible.")) {
                document.getElementById('deleteForm').submit(); // Soumet le formulaire de suppression
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>

    <h1>Détails du Match</h1>
    <!-- Inclusion du fichier PHP qui charge les détails du match -->
    <?php include './../php/details_avant_match.php'; ?>

    <!-- Affichage d'un message d'erreur si nécessaire -->
    <?php if (isset($errorMessage)): ?>
        <p style="color:red;"><?= $errorMessage ?></p>
    <?php else: ?>

        <!-- Alerte en cas de problème avec le nombre de titulaires -->
        <?php if ($error): ?>
            <script>alert('Il doit y avoir exactement 7 titulaires sélectionnés.');</script>
        <?php endif; ?>

        <?php if ($match): ?>
            <!-- Affichage des informations principales du match -->
            <p>Match <?= htmlspecialchars($match['Lieu'] === 'domicile' ? "à domicile" : "à l'extérieur") ?></p>
            <p>Contre <?= htmlspecialchars($match['Nom_adversaire']) ?></p>
            <p>Le <?= (new DateTime($match['Date_Heure']))->format('d/m/Y \à H\hi') ?></p>

            <!-- Formulaire pour modifier la date du match -->
            <form method="POST" action="./../php/enregistrer_avant_match.php">
                <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
                <label for="nouvelle_date">Nouvelle date :</label>
                <input type="datetime-local" name="nouvelle_date" id="nouvelle_date" value="<?= (new DateTime($match['Date_Heure']))->format('Y-m-d\TH:i') ?>" min="<?= date('Y-m-d\TH:i') ?>">
                <button type="submit">Modifier la date</button>
            </form>

            <!-- Liste des joueurs participants -->
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
                                <!-- Informations principales du joueur -->
                                <td><?= htmlspecialchars($player['Nom']) ?></td>
                                <td><?= htmlspecialchars($player['Prénom']) ?></td>
                                <td><?= htmlspecialchars($player['Numéro_de_licence']) ?></td>
                                <td><?= htmlspecialchars(calculateAge($player['Date_de_naissance'])) ?></td>
                                <td><?= htmlspecialchars($player['Taille']) ?></td>
                                <td><?= htmlspecialchars($player['Poid']) ?></td>
                                <td><?= htmlspecialchars($player['Commentaire'] ?? 'Pas de commentaire') ?></td>

                                <!-- Checkbox pour sélectionner un joueur -->
                                <td>
                                    <input type="checkbox" name="choix_<?= htmlspecialchars($player['Id_joueur']) ?>" onclick="toggleComboboxes(this)" <?= $player['assigned'] ? 'checked' : '' ?>>
                                </td>

                                <!-- Combobox pour choisir le poste -->
                                <td>
                                    <select class="poste" style="display: <?= $player['assigned'] ? 'inline' : 'none' ?>;" name="poste_<?= htmlspecialchars($player['Id_joueur']) ?>">
                                        <option value="Attaquant" <?= $player['poste'] === 'Attaquant' ? 'selected' : '' ?>>Attaquant</option>
                                        <option value="Milieu" <?= $player['poste'] === 'Milieu' ? 'selected' : '' ?>>Milieu</option>
                                        <option value="Défenseur" <?= $player['poste'] === 'Défenseur' ? 'selected' : '' ?>>Défenseur</option>
                                        <option value="Gardien" <?= $player['poste'] === 'Gardien' ? 'selected' : '' ?>>Gardien</option>
                                    </select>
                                </td>

                                <!-- Combobox pour choisir le rôle -->
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

    <!-- Formulaire et bouton pour supprimer le match -->
    <form id="deleteForm" method="POST" action="./../php/enregistrer_avant_match.php">
        <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
        <input type="hidden" name="action" value="supprimer">
    </form>
    <button type="button" onclick="confirmDelete()">Supprimer le match</button>
</body>
</html>
