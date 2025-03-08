<?php
require_once 'connection_bd.php';
require_once 'validate_token.php';
session_start();
validate_token();

$joueurs = [];
$error = null;

try {
    $pdo = connectionToDB();

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
