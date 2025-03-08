<?php
session_start();
require_once 'connection_bd.php';
require_once 'validate_token.php';
validate_token();

function calculateAge($date_naissance) {
    try {
        $date_naissance = new DateTime($date_naissance);
        $aujourdhui = new DateTime();
        return $aujourdhui->diff($date_naissance)->y;
    } catch (Exception $e) {
        return 'Inconnu';
    }
}

// Vérifier si l'ID du joueur est référencé dans la table `participer`
function aParticipéAUnMatch($pdo, $idJoueur) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participer WHERE id_joueur = :id");
    $stmt->execute([':id' => $idJoueur]);
    return $stmt->fetchColumn() > 0;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $IdJoueur = intval($_GET['id']);

    try {
        $pdo = connectionToDB();

        $referencedInParticiper = false;

        if (aParticipéAUnMatch($pdo, $IdJoueur)) {
            $referencedInParticiper = true;
        }

        $stmt = $pdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance, Statut 
                               FROM joueur WHERE Id_joueur = :id");

        // je récupère l'id du joueur pour afficher ses informations et les prochaines requetes
        $stmt->execute([':id' => $IdJoueur]);
        $joueur = $stmt->fetch(PDO::FETCH_ASSOC);

        $message = "";
        $nom = $joueur['Nom'];
        $prenom = $joueur['Prénom'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $licence = $_POST['licence'];
            $taille = $_POST['taille'];
            $poid = $_POST['poid'];
            $commentaire = $_POST['commentaire'];
            $status = $_POST['status'];
            // Validation d'un joueur
            if(isset($_POST['Valider'])){
                $updateStmt = $pdo->prepare("UPDATE joueur SET Numéro_de_licence = :licence, Taille = :taille, Poid = :poid, Commentaire = :commentaire, Statut = :status WHERE Id_joueur = :id");
                $updateStmt->execute([      
                    ':licence' => $licence,
                    ':taille' => $taille,
                    ':poid' => $poid,
                    ':commentaire' => $commentaire,
                    ':status' => $status,
                    ':id' => $IdJoueur
                ]);
            }
            // Suppression d'un joueur
            if(isset($_POST['delete'])){
                $deleteStmt = $pdo->prepare("DELETE FROM joueur WHERE Id_joueur = :id");
                $deleteStmt->execute([':id' => $IdJoueur]);
                header("Location: joueur.php");
                exit;
            }
            $joueur['Numéro_de_licence'] = $licence;
            $joueur['Taille'] = $taille;
            $joueur['Poid'] = $poid;
            $joueur['Commentaire'] = $commentaire;
            $joueur['Statut'] = $status;
            $message = "<p>Les informations ont été mises à jour avec succès.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    }
} else {
    echo "<p>Identifiant de match invalide.</p>";
    exit;
}
?>
