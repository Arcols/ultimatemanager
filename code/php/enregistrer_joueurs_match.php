<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'identifiant du match depuis le POST
    $idMatch = $_POST['id_match'] ?? null;

    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Modifier la date du match si elle est fournie
    if (!empty($_POST['nouvelle_date'])) {
        $nouvelleDate = $_POST['nouvelle_date'];
        $stmt = $pdo->prepare("UPDATE Rencontre SET Date_Heure = :nouvelleDate WHERE Id_Match = :idMatch");
        $stmt->execute([
            ':nouvelleDate' => $nouvelleDate,
            ':idMatch' => intval($idMatch),
        ]);
    }

    // Récupérer les joueurs actuellement enregistrés pour ce match
    $stmt = $pdo->prepare("SELECT Id_joueur FROM Participer WHERE Id_Match = :idMatch");
    $stmt->execute([':idMatch' => intval($idMatch)]);
    $joueursExistants = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Préparer une liste des joueurs sélectionnés dans le formulaire
    $joueursSelectionnes = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'choix_') === 0) {
            $idJoueur = substr($key, 6);
            $joueursSelectionnes[] = $idJoueur;

            // Récupérer les autres champs
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

    // Détecter les joueurs qui ne sont plus sélectionnés et les supprimer
    $joueursASupprimer = array_diff($joueursExistants, $joueursSelectionnes);
    if (!empty($joueursASupprimer)) {
        $stmt = $pdo->prepare("DELETE FROM Participer WHERE Id_Match = :idMatch AND Id_joueur = :idJoueur");
        foreach ($joueursASupprimer as $idJoueurASupprimer) {
            $stmt->execute([
                ':idMatch' => intval($idMatch),
                ':idJoueur' => $idJoueurASupprimer,
            ]);
        }
    }

    // Redirection vers la page des détails du match après la soumission
    header("Location: ./../pages/details_match_avant.php?id=" . intval($idMatch));
    exit;
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>
