<?php
session_start();
require 'config.php';

/* Vérifier si connecté */
if (!isset($_SESSION['user'])) {
    header("Location: ../HTML/login.html");
    exit;
}

/* Vérifier si admin */
$sql = $pdo->prepare("SELECT role FROM utilisateurs WHERE nom = ?");
$sql->execute([$_SESSION['user']]);
$user = $sql->fetch();

if ($user['role'] !== 'admin') {
    die("Accès refusé");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>

<header></header>

<nav>
    <a href="../index.html">Accueil</a>
    <a href="../location.html">Location</a>
    <a href="logout.php">Déconnexion</a>
</nav>

<main>

<h1>Panel Admin</h1>

<!-- UTILISATEURS -->
<section class="carte">
    <h2>Utilisateurs</h2>

    <?php
    $users = $pdo->query("SELECT * FROM utilisateurs");

    while ($u = $users->fetch()) {
        echo "<p>{$u['nom']} ({$u['email']}) 
        <a class='bouton' href='delete_user.php?id={$u['id']}'>Supprimer</a></p>";
    }
    ?>
</section>

<section class="carte">
    <h2>Réservations</h2>

    <?php
    $res = $pdo->query("SELECT * FROM reservations");

    while ($r = $res->fetch()) {
        echo "<p>Voiture: {$r['voiture']} - Date: {$r['date_reservation']}
        <a class='bouton' href='delete_reservation.php?id={$r['id']}'>Supprimer</a></p>";
    }
    ?>
</section>

</main>

</body>
</html>