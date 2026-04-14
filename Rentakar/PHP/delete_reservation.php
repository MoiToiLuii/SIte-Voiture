<?php
session_start();
require 'config.php';

/* Vérif admin */
$sql = $pdo->prepare("SELECT role FROM utilisateurs WHERE nom = ?");
$sql->execute([$_SESSION['user']]);
$user = $sql->fetch();

if ($user['role'] !== 'admin') {
    die("Accès refusé");
}

/* Suppression */
if (isset($_GET['id'])) {
    // Avant d'exécuter la suppression, forcer le type entier :
    $id = (int) $_GET['id'];  // cast en int — neutralise toute injection

    $delete = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $delete->execute([$id]);

    header("Location: admin.php");
}
?>