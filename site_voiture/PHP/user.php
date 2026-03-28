<?php
include 'traitements.php';

// Récupérer tous les utilisateurs (nom et email) depuis la base de données
$stmt = $pdo->query("SELECT nom, email FROM utilisateurs");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération sous forme de tableau associatif
?>
<!DOCTYPE html>
<html>
<head>
    <title>Liste des utilisateurs</title>
</head>
<body>
    <h1>Utilisateurs</h1>
    <ul>
        <?php foreach ($users as $user): // Boucle sur chaque utilisateur ?>
            <!-- Affichage sécurisé avec htmlspecialchars pour éviter les failles XSS -->
            <li><?= htmlspecialchars($user['nom']) ?> - <?= htmlspecialchars($user['email']) ?></li>
        <?php endforeach; ?>
    </ul>
    <!-- Lien de retour vers la page d'accueil -->
    <a href="index.html">Retour à l'accueil</a>
</body>
</html>