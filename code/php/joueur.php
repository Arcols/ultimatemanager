<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header("Location: connexion.html.php");
    exit;
}

$joueurs = [];
$error = null;

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les données des joueurs
    $stmt = $pdo->query("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Statut, Date_de_naissance FROM joueur");
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fonction pour calculer l'âge
    function calculateAge($date_naissance) {
        $date_naissance = new DateTime($date_naissance);
        $today = new DateTime();
        return $today->diff($date_naissance)->y;
    }

} catch (PDOException $e) {
    $error = "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
