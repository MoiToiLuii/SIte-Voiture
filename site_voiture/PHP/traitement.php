<?php
// Inclusion du fichier de connexion à la base de données et des fonctions utilitaires
include 'traitements.php';

// Exécution de la requête SQL pour récupérer le nom et l'email de tous les utilisateurs
$stmt = $pdo->query("SELECT nom, email FROM utilisateurs");

// Stockage des résultats dans un tableau associatif
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
        <?php // Parcours de chaque utilisateur récupéré depuis la base de données
        foreach ($users as $user): ?>
            <!-- Affichage du nom et de l'email, échappés pour prévenir les failles XSS -->
            <li><?= htmlspecialchars($user['nom']) ?> - <?= htmlspecialchars($user['email']) ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Lien de navigation vers la page d'accueil -->
    <a href="index.html">Retour à l'accueil</a>
</body>
</html>