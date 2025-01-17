<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Joueur</title>
    
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/details_joueur.css">
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    
    <main>
        <!-- Inclusion du fichier PHP qui récupère les détails du joueur -->
        <?php include './../php/details_joueur.php'; ?>

        <!-- Vérification si un joueur a été trouvé -->
        <?php if (!empty($joueur)): ?>
            <?= $message ?>

            <!-- Affichage des informations du joueur -->
            <h1><?= htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8') ?></h1>
            
            <!-- Formulaire permettant de modifier les informations du joueur -->
            <form method="POST" action="">
                <label for="licence">Licence :</label>
                <input type="text" id="licence" name="licence" value="<?= htmlspecialchars($joueur['Numéro_de_licence'], ENT_QUOTES, 'UTF-8') ?>">

                <label>Âge :</label>
                <p> <?= calculateAge($joueur['Date_de_naissance']) ?></p>

                <label for="taille">Taille (m) :</label>
                <input type="number" id="taille" name="taille" step="0.01" value="<?= htmlspecialchars($joueur['Taille'], ENT_QUOTES, 'UTF-8') ?>">

                <label for="poid">Poids (kg) :</label>
                <input type="number" id="poid" name="poid" value="<?= htmlspecialchars($joueur['Poid'], ENT_QUOTES, 'UTF-8') ?>">

                <label for="commentaire">Commentaire :</label>
                <textarea id="commentaire" name="commentaire"><?= htmlspecialchars($joueur['Commentaire'], ENT_QUOTES, 'UTF-8') ?></textarea>

                <!-- Sélecteur pour choisir le statut du joueur -->
                <label for="status">Statut :</label>
                <select id="status" name="status">
                    <option value="actif" <?= $joueur['Statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="blesse" <?= $joueur['Statut'] === 'blesse' ? 'selected' : '' ?>>Blessé</option>
                    <option value="suspendu" <?= $joueur['Statut'] === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                    <option value="absent" <?= $joueur['Statut'] === 'absent' ? 'selected' : '' ?>>Absent</option>
                </select>

                <!-- Bouton pour valider les modifications -->
                <button type="submit" name="Valider">Valider</button>

                <!-- Bouton pour supprimer le joueur, uniquement si le joueur n'est pas référencé dans un autre contexte (par exemple, une participation) -->
                <?php if (!$referencedInParticiper): ?>
                    <button type="submit" name="delete" value="delete" class="deleteButton" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');">Supprimer le joueur</button>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <!-- Message affiché si aucun joueur n'a été trouvé -->
            <p style="text-align:center;">Aucun joueur trouvé.</p>
        <?php endif; ?>
    </main>
</body>
</html>
