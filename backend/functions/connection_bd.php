<?php
function connectionToDB()
{
    // Définition des informations de connexion à la base de données
    $host = "mysql-backend-ultimate-manager.alwaysdata.net";
    $dbname = "backend-ultimate-manager_bd";
    $username = "404991";
    $password = '$iutinfo';

    try {
        // Tentative de connexion à la base de données
        $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);
        // Définition des attributs pour une meilleure gestion des erreurs
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Retourner une réponse d'erreur sans exposer les détails techniques
        // pour des raisons de sécurité
        die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
    }
}

