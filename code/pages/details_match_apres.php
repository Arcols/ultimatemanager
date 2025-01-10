<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMatch = intval($_GET['id']);

    try {
        // Connexion à la base de données
        $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour récupérer les informations du match
        $stmt = $pdo->prepare("SELECT * FROM rencontre WHERE Id_Match = :id");
        $stmt->execute([':id' => $idMatch]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match) {
            // Vérifier si la date du match est après la date actuelle
            $dateMatch = new DateTime($match['Date_Heure']);
            $currentDate = new DateTime();
            
            // Si la date du match est après la date actuelle, rediriger vers la page "detail_match_apres.php"
            if ($dateMatch > $currentDate) {
                header("Location: details_match_avant.php?id=" . $idMatch);
                exit;
            }
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
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
            $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!isset($_GET['id']) || empty($_GET['id'])) {
                echo "<p style='color:red;'>Aucun match spécifié.</p>";
                exit;
            }
            $idMatch = intval($_GET['id']);

            $stmt = $pdo->prepare("SELECT Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre WHERE Id_Match = :idMatch");
            $stmt->execute([':idMatch' => $idMatch]);
            $match = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$match) {
                echo "<p style='color:red;'>Match introuvable.</p>";
                exit;
            }

            $dateTime = new DateTime($match['Date_Heure']);
            $formattedDateTime = $dateTime->format('d/m/Y à H\hi');

            $lieu = $match['Lieu'] === 'domicile' ? 'Match à domicile' : 'Match à l\'extérieur';

            echo "<p><strong>Date :</strong> $formattedDateTime</p>";
            echo "<p><strong>Lieu :</strong> $lieu</p>";
            echo "<p><strong>Adversaire :</strong> " . htmlspecialchars($match['Nom_adversaire']) . "</p>";

            echo "<form method='POST' action='./../php/enregistrer_match.php'>";
            echo "<input type='hidden' name='id_match' value='" . htmlspecialchars($idMatch) . "'>";

            $resultat = isset($match['Résultat']) && strpos($match['Résultat'], ':') !== false 
            ? explode(':', $match['Résultat']) 
            : [0, 0];

            $score1 = htmlspecialchars($resultat[0]);
            $score2 = htmlspecialchars($resultat[1]);

            echo "<div class='score-input'>";
            echo "<label for='score1'>Score :</label>";
            echo "<input type='number' name='score1' id='score1' value='$score1' min='0' max='15'>";
            echo "<span>:</span>";
            echo "<input type='number' name='score2' id='score2' value='$score2' min='0' max='15'>";
            echo "</div>";

            $stmtParticipants = $pdo->prepare("SELECT J.Id_joueur, J.Nom, J.Prénom, P.Poste, P.Role AS Role, P.Note FROM Joueur J JOIN Participer P ON J.Id_joueur = P.Id_joueur WHERE P.Id_Match = :idMatch");
            $stmtParticipants->execute([':idMatch' => $idMatch]);
            $participants = $stmtParticipants->fetchAll(PDO::FETCH_ASSOC);

            if ($participants) {
                echo "<h2>Joueurs ayant participé au match</h2>";
                echo "<table>
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

                const stars = this.parentNode.querySelectorAll('.star');
                stars.forEach(s => s.classList.remove('active'));
                for (let i = 0; i < note; i++) {
                    stars[i].classList.add('active');
                }

                const input = this.parentNode.nextElementSibling;
                if (input) {
                    input.value = note;
                }
            });
        });
    </script>
</body>
</html>
