<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.html.php");
    exit;
}

// Vérifier si un message d'erreur est passé dans l'URL
$error = isset($_GET['error']) && $_GET['error'] === 'titulaires';

// Vérifier si un identifiant de match est passé dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMatch = intval($_GET['id']);
    $match = null;
    $players = [];

    try {
        // Connexion à la base de données
        $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupération des informations du match
        $stmt = $pdo->prepare("SELECT * FROM rencontre WHERE Id_Match = :id");
        $stmt->execute([':id' => $idMatch]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match) {
            $dateMatch = new DateTime($match['Date_Heure']);
            $currentDate = new DateTime();
            if ($dateMatch < $currentDate) {
                header("Location: details_apres_match.html.php?id=" . $idMatch);
                exit;
            }
        }

        // Récupération des joueurs actifs
        $stmt = $pdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance FROM joueur WHERE Statut = 'Actif'");
        $stmt->execute();
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Vérification des joueurs assignés au match
        foreach ($players as &$player) {
            $stmtCheck = $pdo->prepare("SELECT Poste, Role FROM participer WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
            $stmtCheck->execute([':idJoueur' => $player['Id_joueur'], ':idMatch' => $idMatch]);
            $assigned = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            $player['assigned'] = $assigned ? true : false;
            $player['poste'] = $assigned['Poste'] ?? '';
            $player['role'] = $assigned['Role'] ?? '';
        }
    } catch (PDOException $e) {
        $errorMessage = htmlspecialchars($e->getMessage());
    }
} else {
    $idMatch = null;
    $match = null;
    $players = [];
    $errorMessage = "Identifiant de match invalide.";
}

// Fonction pour calculer l'âge
function calculateAge($date_naissance) {
    $date_naissance = new DateTime($date_naissance);
    $today = new DateTime();
    return $today->diff($date_naissance)->y;
}

