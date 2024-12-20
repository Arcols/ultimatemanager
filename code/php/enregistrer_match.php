<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'identifiant du match depuis le POST
    $idMatch = $_POST['id_match'] ?? null;

    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Parcourir les données POST pour enregistrer les joueurs sélectionnés
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'choix_') === 0) {
            $idJoueur = substr($key, 6);
            $poste = $_POST['poste_' . $idJoueur] ?? '';
            $role = $_POST['role_' . $idJoueur] ?? '';

            // Requête pour insérer ou mettre à jour le joueur dans la table "Participer"
            $stmt = $pdo->prepare("
                INSERT INTO Participer (Id_joueur, Id_Match, Poste, Role)
                VALUES (:idJoueur, :idMatch, :poste, :role)
                ON DUPLICATE KEY UPDATE Poste = :poste, Role = :role
            ");
            $stmt->execute([
                ':idJoueur' => $idJoueur,
                ':idMatch' => intval($idMatch),
                ':poste' => $poste,
                ':role' => $role,
            ]);
        }
    }

    // Redirection vers la page des détails du match après la soumission
    header("Location: ./../pages/details_match_apres.php?id=" . intval($idMatch));
    exit;
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>