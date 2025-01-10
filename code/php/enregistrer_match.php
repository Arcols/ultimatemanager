<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'identifiant du match depuis le POST
    $idMatch = $_POST['id_match'] ?? null;

    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Mise à jour du score dans la table "Rencontre"
    $score1 = $_POST['score1'] ?? null;
    $score2 = $_POST['score2'] ?? null;

    if (isset($score1, $score2)) {
        $resultat = $score1 . ':' . $score2;

        $stmtUpdateScore = $pdo->prepare("
            UPDATE Rencontre
            SET Résultat = :resultat
            WHERE Id_Match = :idMatch
        ");
        $stmtUpdateScore->execute([
            ':resultat' => $resultat,
            ':idMatch' => intval($idMatch),
        ]);
    }

    // Mise à jour des notes des joueurs dans la table "Participer"
    if (isset($_POST['notes']) && is_array($_POST['notes'])) {
        foreach ($_POST['notes'] as $idJoueur => $note) {
            $stmtUpdateNote = $pdo->prepare("
                UPDATE Participer
                SET Note = :note
                WHERE Id_joueur = :idJoueur AND Id_Match = :idMatch
            ");
            $stmtUpdateNote->execute([
                ':note' => intval($note),
                ':idJoueur' => intval($idJoueur),
                ':idMatch' => intval($idMatch),
            ]);
        }
    }

    // Redirection vers la page des détails du match après la soumission
    header("Location: ./../pages/matchs.php?id=" . intval($idMatch));
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>
