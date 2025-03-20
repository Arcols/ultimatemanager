<?php
function connectionToDB()
{
    $host = "mysql-ultimatemanager.alwaysdata.net";
    $dbname = "ultimatemanager_bdd";
    $username = "385401";
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