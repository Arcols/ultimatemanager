<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les données POST
    $idMatch = $_POST['idMatch'] ?? null;
    $resultat = $_POST['resultat'] ?? '';
    $notes = $_POST['notes'] ?? [];

    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Mettre à jour le résultat du match
    $stmt = $pdo->prepare("UPDATE Rencontre SET Résultat = :resultat WHERE Id_Match = :idMatch");
    $stmt->execute([':resultat' => $resultat, ':idMatch' => intval($idMatch)]);

    // Mettre à jour les notes des joueurs
    $stmtNote = $pdo->prepare("UPDATE Participer SET Note = :note WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch");
    foreach ($notes as $idJoueur => $note) {
        $stmtNote->execute([':note' => $note, ':idJoueur' => $idJoueur, ':idMatch' => intval($idMatch)]);
    }

    echo "Données enregistrées avec succès.";
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
