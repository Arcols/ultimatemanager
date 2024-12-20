<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit;
}

function calculateAge($date_naissance) {
    try {
        $date_naissance = new DateTime($date_naissance);
        $aujourdhui = new DateTime();
        return $aujourdhui->diff($date_naissance)->y;
    } catch (Exception $e) {
        return 'Inconnu';
    }
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $IdJoueur = intval($_GET['id']);

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=ultimatemanagerbdd;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT Id_joueur, Numéro_de_licence, Nom, Prénom, Taille, Poid, Commentaire, Date_de_naissance, Statut 
                               FROM joueur WHERE Id_joueur = :id");
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

            $updateStmt = $pdo->prepare("UPDATE joueur SET Numéro_de_licence = :licence, Taille = :taille, Poid = :poid, Commentaire = :commentaire, Statut = :status WHERE Id_joueur = :id");
            $updateStmt->execute([
                
                ':licence' => $licence,
                ':taille' => $taille,
                ':poid' => $poid,
                ':commentaire' => $commentaire,
                ':status' => $status,
                ':id' => $IdJoueur
            ]);

            $joueur['Numéro_de_licence'] = $licence;
            $joueur['Taille'] = $taille;
            $joueur['Poid'] = $poid;
            $joueur['Commentaire'] = $commentaire;
            $joueur['Statut'] = $status;

            $message = "<p style='color:green;'>Les informations ont été mises à jour avec succès.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    }
} else {
    echo "<p style='text-align:center;'>Identifiant de match invalide.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Manager - Joueur</title>
    <link rel="stylesheet" href="./../css/global.css">
    <link rel="stylesheet" href="./../css/details_joueur.css">
</head>
<body>
    <div class="header">
        <?php include './../headfoot/header.html'; ?>
    </div>
    <main>
        <?php if (!empty($joueur)): ?>
            <h1><?= htmlspecialchars($joueur['Nom'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($joueur['Prénom'], ENT_QUOTES, 'UTF-8') ?></h1>
                <form method="POST" action="">

                <label for="licence">Licence :</label>
                <input type="text" id="licence" name="licence" value="<?= htmlspecialchars($joueur['Numéro_de_licence'], ENT_QUOTES, 'UTF-8') ?>">

                <label>Âge :</label>
                <p> <?= calculateAge($joueur['Date_de_naissance']) ?></p>

                <label for="taille">Taille (m) :</label>
                <input type="number" id="taille" name="taille" step="0.01" value="<?= htmlspecialchars($joueur['Taille'], ENT_QUOTES, 'UTF-8') ?>">

                <label for="poid">Poids (kg) :</label>
                <input type="number" id="poid" name="poid" value="<?= htmlspecialchars($joueur['Poid'], ENT_QUOTES, 'UTF-8') ?>">

                <label for="commentaire">Commentaire :</label>
                <textarea id="commentaire" name="commentaire"><?= htmlspecialchars($joueur['Commentaire'], ENT_QUOTES, 'UTF-8') ?></textarea>

                <label for="status">Statut :</label>
                <select id="status" name="status">
                    <option value="actif" <?= $joueur['Statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="blesse" <?= $joueur['Statut'] === 'blesse' ? 'selected' : '' ?>>Blessé</option>
                    <option value="suspendu" <?= $joueur['Statut'] === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                    <option value="absent" <?= $joueur['Statut'] === 'absent' ? 'selected' : '' ?>>Absent</option>
                </select>
                <button type="submit">Valider</button>
                <?= $message ?>

            </form>
        <?php else: ?>
            <p style="text-align:center;">Aucun joueur trouvé.</p>
        <?php endif; ?>
    </main>
</body>
</html>
