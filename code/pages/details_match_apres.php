<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Détails du Match</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/header.css">
    <link rel="stylesheet" href="./../css/match_apres.css">

</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <div class="main">
        <h1>Détails du Match</h1>

        <?php
        try {
            // Connexion à la base de données
            $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Récupérer l'ID du match depuis l'URL
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                echo "<p style='color:red;'>Aucun match spécifié.</p>";
                exit;
            }
            $idMatch = intval($_GET['id']);

            // Récupérer les détails du match
            $stmt = $pdo->prepare("SELECT Date_Heure, Lieu, Nom_adversaire, Résultat FROM Rencontre WHERE Id_Match = :idMatch");
            $stmt->execute([':idMatch' => $idMatch]);
            $match = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$match) {
                echo "<p style='color:red;'>Match introuvable.</p>";
                exit;
            }

            // Formatage de la date et de l'heure
            $dateTime = new DateTime($match['Date_Heure']);
            $formattedDateTime = $dateTime->format('d/m/Y à H\hi');

            // Déterminer si c'est un match à domicile ou à l'extérieur
            $lieu = $match['Lieu'] === 'domicile' ? 'Match à domicile' : 'Match à l\'extérieur';

            echo "<p><strong>Date :</strong> $formattedDateTime</p>";
            echo "<p><strong>Lieu :</strong> $lieu</p>";
            echo "<p><strong>Adversaire :</strong> " . htmlspecialchars($match['Nom_adversaire']) . "</p>";

            // Formulaire pour les données éditables
            echo "<form method='POST' action='./../php/enregistrer_match.php'>";

            // Champ caché pour transmettre l'ID du match
            echo "<input type='hidden' name='idMatch' value='" . htmlspecialchars($idMatch) . "'>";

            // Champ pour le résultat
            echo "<div class='score-input'>";
            echo "<label for='resultat'>Résultat :</label>";
            echo "<input type='text' name='resultat' id='resultat' value='" . htmlspecialchars($match['Résultat'] ?? '') . "'>";
            echo "</div>";

            // Récupérer les joueurs ayant participé au match
            $stmtParticipants = $pdo->prepare("
                SELECT J.Id_joueur, J.Nom, J.Prénom, P.Poste, P.T_R AS Role, P.Note
                FROM Joueur J
                JOIN Participer P ON J.Id_joueur = P.Id_joueur
                WHERE P.Id_Match = :idMatch
            ");
            $stmtParticipants->execute([':idMatch' => $idMatch]);
            $participants = $stmtParticipants->fetchAll(PDO::FETCH_ASSOC);

            if ($participants) {
                echo "<h2>Joueurs ayant participé au match</h2>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 20px;'>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Poste</th>
                                <th>Rôle</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>";

                foreach ($participants as $participant) {
                    $idJoueur = intval($participant['Id_joueur']);
                    $nom = htmlspecialchars($participant['Nom']);
                    $prenom = htmlspecialchars($participant['Prénom']);
                    $poste = htmlspecialchars($participant['Poste']);
                    $role = htmlspecialchars($participant['Role']);
                    $note = intval($participant['Note']);

                    // Étoiles interactives pour la note
                    $etoiles = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $activeClass = $i <= $note ? 'active' : '';
                        $etoiles .= "<span class='star $activeClass' data-note='$i' data-player-id='$idJoueur'>&#9733;</span>";
                    }

                    echo "<tr>
                            <td>$nom</td>
                            <td>$prenom</td>
                            <td>$poste</td>
                            <td>$role</td>
                            <td>
                                <div class='stars' data-player-id='$idJoueur'>$etoiles</div>
                                <input type='hidden' name='notes[$idJoueur]' value='$note'>
                            </td>
                          </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Aucun joueur n'a participé à ce match.</p>";
            }

            // Bouton de validation
            echo "<div class='submit-container'>";
            echo "<button type='submit'>Valider</button>";
            echo "</div>";

            echo "</form>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <script>
        document.querySelectorAll('.stars .star').forEach(star => {
            star.addEventListener('click', function () {
                const note = this.getAttribute('data-note');
                const playerId = this.getAttribute('data-player-id');

                // Mettre à jour l'apparence des étoiles
                const stars = this.parentNode.querySelectorAll('.star');
                stars.forEach(s => s.classList.remove('active'));
                for (let i = 0; i < note; i++) {
                    stars[i].classList.add('active');
                }

                // Mettre à jour la valeur cachée de la note
                const input = this.parentNode.nextElementSibling;
                if (input) {
                    input.value = note;
                }
            });
        });
    </script>
</body>
</html>
