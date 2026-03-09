<?php
include 'traitements.php';

// Récupérer tous les utilisateurs
$stmt = $pdo->query("SELECT nom, email FROM utilisateurs");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Liste des utilisateurs</title>
</head>
<body>
    <h1>Utilisateurs</h1>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?= htmlspecialchars($user['nom']) ?> - <?= htmlspecialchars($user['email']) ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="index.html">Retour à l’accueil</a>
</body>
</html>
