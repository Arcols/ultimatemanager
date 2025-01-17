<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.html.php");
    exit;
}

$idMatch = null;
$match = null;
$participants = [];
$errorMessage = null;

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

            if ($dateMatch > $currentDate) {
                header("Location: details_avant_match.html.php?id=" . $idMatch);
                exit;
            }

            // Récupération des participants
            $stmtParticipants = $pdo->prepare("SELECT J.Id_joueur, J.Nom, J.Prénom, P.Poste, P.Role AS Role, P.Note 
                                                FROM joueur J, participer P
                                                WHERE J.Id_joueur = P.Id_joueur 
                                                AND P.Id_Match = :idMatch");
            $stmtParticipants->execute([':idMatch' => $idMatch]);
            $participants = $stmtParticipants->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $errorMessage = "Match introuvable.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Erreur : " . htmlspecialchars($e->getMessage());
    }
} else {
    $errorMessage = "Aucun match spécifié.";
}
?>
