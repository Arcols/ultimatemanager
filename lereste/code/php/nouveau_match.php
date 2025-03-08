<?php
require_once 'connection_bd.php';
require_once 'validate_token.php';
validate_token();
// Connexion à la base de données
try {
    $pdo = connectionToDB();

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si tous les champs sont remplis
    if (isset($_POST['date_heure'], $_POST['nom_adversaires'], $_POST['lieu'])) {
        // Récupérer les données du formulaire
        $date_heure = $_POST['date_heure'];
        $nom_adversaires = htmlspecialchars($_POST['nom_adversaires']);
        $lieu = htmlspecialchars($_POST['lieu']);
        $resultat1 = htmlspecialchars($_POST['resultat1']);
        $resultat2 = htmlspecialchars($_POST['resultat2']);
        if (!empty($resultat1) && !empty($resultat2) && is_numeric($resultat1) && is_numeric($resultat2)) {
            $resultat = $resultat1 . " : " . $resultat2;
        } elseif (empty($resultat1) && empty($resultat2)) {
            $resultat = null;
        }else{
            header('Location: ./../pages/matchs.html.php?error=Veuillez remplir les deux champs résultat ou aucun des deux');
            exit;
        }
        try {
            // Requête préparée pour insérer un joueur
            $stmt = $pdo->prepare("INSERT INTO rencontre (Date_heure, Nom_adversaire, Lieu, Résultat) 
                                   VALUES (:date_heure, :nom_adversaires, :lieu, :resultat)");

            $stmt->execute([
                ':date_heure' => $date_heure,
                ':nom_adversaires' => $nom_adversaires,
                ':lieu' => $lieu,
                ':resultat' => $resultat,
                
            ]);

            // Rediriger vers matchs.html.php après ajout avec un message de succès
            header('Location: ./../pages/matchs.html.php?success=1');
            exit;
        } catch (PDOException $e) {
            echo "<p>Erreur lors de l'ajout : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>Veuillez remplir tous les champs du formulaire.</p>";
    }
}
?>
