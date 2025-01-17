<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Joueurs</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/joueur.css">
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    
    <div class="main">
        <!-- Titre de la page -->
        <h1>Gestion des Joueurs</h1>

        <!-- Inclusion du fichier PHP qui récupère les joueurs -->
        <?php include './../php/joueur.php'; ?>

        <?php if ($error): ?>
            <p><?= $error ?></p>
        <?php else: ?>
            <!-- Table des joueurs -->
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Licence</th>
                        <th>Âge</th>
                        <th>Taille (m)</th>
                        <th>Poids (kg)</th>
                        <th>Commentaire</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                <!-- Affichage des informations de chaque joueur -->
                <?php foreach ($joueurs as $joueur): ?>
                    <tr onclick="window.location.href = 'details_joueur.html.php?id=<?= htmlspecialchars($joueur['Id_joueur'], ENT_QUOTES, 'UTF-8') ?>';">
                        <td><?= htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Numéro_de_licence'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(calculateAge($joueur['Date_de_naissance'])) ?></td>
                        <td><?= htmlspecialchars($joueur['Taille'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Poid'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Commentaire'] ?? 'Pas de commentaire', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($joueur['Statut'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Section pour ajouter un joueur -->
        <h2>Ajouter un joueur</h2>

        <!-- Formulaire d'ajout d'un joueur -->
        <form method="POST" action="./../php/ajout_joueur.php">
            <!-- Champs pour les informations du joueur -->
            <p>
                <label for="nom">Nom du Joueur</label>
                <input type="text" id="nom" name="nom" required>
            </p>
            <p>
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
            </p>
            <p>
                <label for="prénom">Prénom du Joueur</label>
                <input type="text" id="prénom" name="prénom" required>
            </p>
            <p>
                <label for="taille">Taille (m)</label>
                <input type="number" id="taille" name="taille" step="0.01" required>
            </p>
            <p>
                <label for="numLic">Numéro de licence</label>
                <input type="text" id="numLic" name="numLic" required>
            </p>
            <p>
                <label for="poid">Poids (kg)</label>
                <input type="number" id="poid" name="poid" step="0.1" required>
            </p>
            <p>
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire"></textarea>
            </p>
            <p>
                <label for="statut">Statut</label>
                <!-- Sélecteur pour le statut du joueur -->
                <select id="statut" name="statut" required>
                    <option value="Actif">Actif</option>
                    <option value="Blessé">Blessé</option>
                    <option value="Suspendu">Suspendu</option>
                    <option value="Absent">Absent</option>
                </select>
            </p>
            <!-- Bouton pour soumettre le formulaire -->
            <button type="submit">Ajouter Joueur</button>
        </form>
    </div>
</body>
</html>
