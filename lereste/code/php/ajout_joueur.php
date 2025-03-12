<?php
require_once 'connection_bd.php';
// Connexion à la base de données
try {
    $pdo = connectionToDB();
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si tous les champs sont remplis
    if (isset($_POST['nom'], $_POST['prénom'], $_POST['date_naissance'], $_POST['taille'], $_POST['poid'], $_POST['commentaire'], $_POST['statut'])) {
        // Récupérer les données du formulaire
        $numLic = htmlspecialchars($_POST['numLic']);
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prénom']);
        $date_naissance = $_POST['date_naissance'];
        $taille = htmlspecialchars($_POST['taille']);
        $poid = htmlspecialchars($_POST['poid']);
        $commentaire = htmlspecialchars($_POST['commentaire']);
        $statut = htmlspecialchars($_POST['statut']);

        try {
            // Requête préparée pour insérer un joueur
            $stmt = $pdo->prepare("INSERT INTO joueur (Numéro_de_licence, Nom, Prénom, Date_de_naissance, Taille, Poid, Commentaire, Statut) 
                                   VALUES (:numLic,:nom, :prenom, :date_naissance, :taille, :poid, :commentaire, :statut)");

            $stmt->execute([
                ':numLic' => $numLic,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':date_naissance' => $date_naissance,
                ':taille' => $taille,
                ':poid' => $poid,
                ':commentaire' => $commentaire,
                ':statut' => $statut
            ]);

            header('Location: ./../pages/joueurs.html.php?success=1');
            exit;
        } catch (PDOException $e) {
            echo "<p" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>Veuillez remplir tous les champs du formulaire.</p>";
    }
}
?>
