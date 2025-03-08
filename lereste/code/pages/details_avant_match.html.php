<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/match_avant.css">
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
        <p><?= $errorMessage ?></p>
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
            <form method="POST" action="../php/enregistrer_avant_match.php">
                <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
                <label for="nouvelle_date">Nouvelle date :</label>
                <input type="datetime-local" name="nouvelle_date" id="nouvelle_date" value="<?= (new DateTime($match['Date_Heure']))->format('Y-m-d\TH:i') ?>" min="<?= date('Y-m-d\TH:i') ?>">
                <button type="submit">Modifier la date</button>
            </form>

            <!-- Liste des joueurs participants -->
            <h2>Joueurs ayant participé</h2>
            <form method="POST" action="../php/enregistrer_avant_match.php">
                <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
                <table border="1">
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
                        <?php foreach ($players as $joueur): ?>
                            <tr>
                                <!-- Informations principales du joueur -->
                                <td><?= htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($joueur['Numéro_de_licence'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(calculateAge($joueur['Date_de_naissance'])) ?></td>
                                <td><?= htmlspecialchars($joueur['Taille'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($joueur['Poid'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($joueur['Commentaire'] ?? 'Pas de commentaire', ENT_QUOTES, 'UTF-8') ?></td>
                        
                                <!-- Checkbox pour sélectionner un joueur -->
                                <td>
                                    <input type="checkbox" name="choix_<?= htmlspecialchars($joueur['Id_joueur']) ?>" onclick="toggleComboboxes(this)" <?= $joueur['assigned'] ? 'checked' : '' ?>>
                                </td>

                                <!-- Combobox pour choisir le poste -->
                                <td>
                                    <select class="poste" style="display: <?= $joueur['assigned'] ? 'inline' : 'none' ?>;" name="poste_<?= htmlspecialchars($joueur['Id_joueur']) ?>">
                                        <option value="Attaquant" <?= $joueur['poste'] === 'Attaquant' ? 'selected' : '' ?>>Attaquant</option>
                                        <option value="Milieu" <?= $joueur['poste'] === 'Milieu' ? 'selected' : '' ?>>Milieu</option>
                                        <option value="Défenseur" <?= $joueur['poste'] === 'Défenseur' ? 'selected' : '' ?>>Défenseur</option>
                                        <option value="Gardien" <?= $joueur['poste'] === 'Gardien' ? 'selected' : '' ?>>Gardien</option>
                                    </select>
                                </td>

                                <!-- Combobox pour choisir le rôle -->
                                <td>
                                    <select class="role" style="display: <?= $joueur['assigned'] ? 'inline' : 'none' ?>;" name="role_<?= htmlspecialchars($joueur['Id_joueur']) ?>">
                                        <option value="Titulaire" <?= $joueur['role'] === 'Titulaire' ? 'selected' : '' ?>>Titulaire</option>
                                        <option value="Remplaçant" <?= $joueur['role'] === 'Remplaçant' ? 'selected' : '' ?>>Remplaçant</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        <?php else: ?>
            <p>Match introuvable.</p>
        <?php endif; ?>
    <?php endif; ?>
    <div class="button-container">
        <button type="submit">Valider</button>
        <form id="deleteForm" method="POST" action="../php/enregistrer_avant_match.php">
            <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">
            <input type="hidden" name="action" value="supprimer">
        </form>
        <button type="button" onclick="confirmDelete()">Supprimer le match</button>
    </div>    
</body>
</html>
