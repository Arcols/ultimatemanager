<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Détails du Match</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/match_apres.css">
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <div class="main">
        <h1>Détails du Match</h1>
        <!-- Inclusion du fichier PHP qui gère la logique des détails du match -->
        <?php include './../php/details_apres_match.php'; ?>

        <!-- Affichage des messages d'erreur ou des informations du match -->
        <?php if ($errorMessage): ?>
            <p><?= htmlspecialchars($errorMessage) ?></p>
        <?php elseif ($match): ?>
            <!-- Affichage des informations principales du match -->
            <p><strong>Date :</strong> <?= (new DateTime($match['Date_Heure']))->format('d/m/Y à H\hi') ?></p>
            <p><strong>Lieu :</strong> <?= $match['Lieu'] === 'domicile' ? 'Match à domicile' : 'Match à l\'extérieur' ?></p>
            <p><strong>Adversaire :</strong> <?= htmlspecialchars($match['Nom_adversaire']) ?></p>

            <!-- Formulaire pour enregistrer les détails du match (score, notes, etc.) -->
            <form method="POST" action="../php/enregistrer_apres_match.php">
                <input type="hidden" name="id_match" value="<?= htmlspecialchars($idMatch) ?>">

                <!-- Section pour saisir le score -->
                <div class="score-input">
                    <?php
                    // Extraction des scores actuels ou définition des scores par défaut
                    $resultat = isset($match['Résultat']) && strpos($match['Résultat'], ':') !== false 
                        ? explode(':', $match['Résultat']) 
                        : [0, 0];
                    ?>
                    <label for="score1">Score :</label>
                    <input type="number" name="score1" id="score1" value="<?= htmlspecialchars($resultat[0]) ?>" min="0" max="15">
                    <span>:</span>
                    <input type="number" name="score2" id="score2" value="<?= htmlspecialchars($resultat[1]) ?>" min="0" max="15">
                </div>

                <!-- Section pour afficher les joueurs ayant participé au match -->
                <?php if ($participants): ?>
                    <h2>Joueurs ayant participé au match</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Poste</th>
                                <th>Rôle</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): ?>
                                <tr>
                                    <!-- Affichage des informations des joueurs -->
                                    <td><?= htmlspecialchars($participant['Nom']) ?></td>
                                    <td><?= htmlspecialchars($participant['Prénom']) ?></td>
                                    <td><?= htmlspecialchars($participant['Poste']) ?></td>
                                    <td><?= htmlspecialchars($participant['Role']) ?></td>
                                    <td>
                                        <!-- Système de notation avec des étoiles interactives -->
                                        <div class="stars" data-player-id="<?= htmlspecialchars($participant['Id_joueur']) ?>">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                $active = $i <= $participant['Note'] ? 'active' : '';
                                                echo "<span class='star $active' data-note='$i'>&#9733;</span>";
                                            }
                                            ?>
                                        </div>
                                        <!-- Champ caché pour envoyer la note finale -->
                                        <input type="hidden" name="notes[<?= htmlspecialchars($participant['Id_joueur']) ?>]" value="<?= htmlspecialchars($participant['Note']) ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Message si aucun joueur n'a participé au match -->
                    <p>Aucun joueur n'a participé à ce match.</p>
                <?php endif; ?>

                <!-- Bouton pour soumettre les détails du match -->
                <div class="submit-container">
                    <button type="submit">Valider</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Script pour gérer l'interaction avec les étoiles (notation des joueurs) -->
    <script>
        document.querySelectorAll('.stars .star').forEach(star => {
            star.addEventListener('click', function () {
                const note = this.getAttribute('data-note'); 
                const stars = this.parentNode.querySelectorAll('.star');
                stars.forEach(s => s.classList.remove('active'));
                for (let i = 0; i < note; i++) {
                    stars[i].classList.add('active');
                }
                const input = this.parentNode.nextElementSibling;
                if (input) input.value = note;
            });
        });
    </script>
</body>
</html>
