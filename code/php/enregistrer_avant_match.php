<?php
try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=mysql-ultimatemanager.alwaysdata.net;dbname=ultimatemanager_bdd;charset=utf8mb4', '385401', '$iutinfo');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'identifiant du match depuis le POST
    $idMatch = $_POST['id_match'] ?? null;
    if (!$idMatch) {
        throw new Exception("ID du match non spécifié.");
    }

    // Si l'action est de supprimer le match
    if (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        echo("Supprimer");
        // Supprimer les joueurs associés à ce match
        $stmt = $pdo->prepare("DELETE FROM Participer WHERE Id_Match = :idMatch");
        $stmt->execute([':idMatch' => intval($idMatch)]);

        // Supprimer le match
        $stmt = $pdo->prepare("DELETE FROM rencontre WHERE Id_Match = :idMatch");
        $stmt->execute([':idMatch' => intval($idMatch)]);

        // Redirection après la suppression
        header("Location: ./../pages/matchs.html.php");
        exit;  // Assurez-vous d'ajouter exit pour stopper le script après la redirection
    }

    // Modifier la date du match si elle est fournie
    if (!empty($_POST['nouvelle_date'])) {
        $nouvelleDate = $_POST['nouvelle_date'];
        $stmt = $pdo->prepare("UPDATE rencontre SET Date_Heure = :nouvelleDate WHERE Id_Match = :idMatch");
        $stmt->execute([
            ':nouvelleDate' => $nouvelleDate,
            ':idMatch' => intval($idMatch),
        ]);
    }

    // Compter le nombre de titulaires sélectionnés dans le formulaire
    $titulairesSelectionnes = 0;
    $joueursSelectionnes = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'choix_') === 0) {
            $idJoueur = substr($key, 6);
            $joueursSelectionnes[] = $idJoueur;

            // Récupérer le rôle du joueur (titulaire ou remplaçant)
            $role = $_POST['role_' . $idJoueur] ?? '';

            // Si le rôle est "Titulaires", on incrémente le compteur
            if ($role === 'Titulaire') {
                $titulairesSelectionnes++;
            }
        }
    }

    // Vérifier si le nombre de titulaires sélectionnés est exactement 7
    if ($titulairesSelectionnes !== 7) {
        // Si le nombre de titulaires n'est pas 7, rediriger avec un message d'erreur dans l'URL
        header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch) . "&error=titulaires");
        exit;  // Ajouter exit ici pour éviter que le code continue après la redirection
    }

    // Récupérer les joueurs actuellement enregistrés pour ce match
    $stmt = $pdo->prepare("SELECT Id_joueur FROM participer WHERE Id_Match = :idMatch");
    $stmt->execute([':idMatch' => intval($idMatch)]);
    $joueursExistants = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Insérer ou mettre à jour les joueurs sélectionnés dans la table "Participer"
    foreach ($joueursSelectionnes as $idJoueur) {
        $poste = $_POST['poste_' . $idJoueur] ?? '';
        $role = $_POST['role_' . $idJoueur] ?? '';

        // Requête pour insérer ou mettre à jour le joueur dans la table "Participer"
        $stmt = $pdo->prepare("
            INSERT INTO participer (Id_joueur, Id_Match, Poste, Role)
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

    // Détecter les joueurs qui ne sont plus sélectionnés et les supprimer
    $joueursASupprimer = array_diff($joueursExistants, $joueursSelectionnes);
    if (!empty($joueursASupprimer)) {
        $stmt = $pdo->prepare("DELETE FROM participer WHERE Id_Match = :idMatch AND Id_joueur = :idJoueur");
        foreach ($joueursASupprimer as $idJoueurASupprimer) {
            $stmt->execute([
                ':idMatch' => intval($idMatch),
                ':idJoueur' => $idJoueurASupprimer,
            ]);
        }
    }

    // Redirection vers la page des détails du match après la soumission
    header("Location: ./../pages/details_avant_match.html.php?id=" . intval($idMatch));
    exit;  // Assurez-vous d'ajouter exit ici aussi
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
    die("Erreur SQL : " . $e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
