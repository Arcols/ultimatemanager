<?php
function connectionToDB()
{
    $host = "mysql-backend-ultimate-manager.alwaysdata.net";
    $dbname = "backend-ultimate-manager_bd";
    $username = "404991";
    $password = '$iutinfo';
    try {
        $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("<p>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>");
    }
}

?>