<?php
require_once 'connection_bd.php';

session_start();

require_once 'validate_token.php';
validate_token();

try {
    $pdo = connectionToDB();

    // Déterminer le filtre sélectionné
    $filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'tous';

    // Construire la requête en fonction du filtre
    $query = "SELECT Id_Match, Date_Heure, Lieu, Nom_adversaire, Résultat FROM rencontre";
    if ($filtre === 'passes') {
        $query .= " WHERE Date_Heure < NOW()";
    } elseif ($filtre === 'avenir') {
        $query .= " WHERE Date_Heure >= NOW()";
    }
    $query .= " ORDER BY Date_Heure ASC"; // Tri par date croissante

    // Exécuter la requête
    $stmt = $pdo->query($query);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Définir l'heure actuelle
    $currentDateTime = new DateTime();
} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}

// Variables de message d'erreur ou de succès
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$successMessage = isset($_GET['success']) ? "Match ajouté avec succès !" : '';

?>
