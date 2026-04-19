<?php
// ============================================================
// SUPPRESSION D'UN UTILISATEUR – delete_user.php
// Accessible uniquement depuis le panel admin (role = 'admin').
// Reçoit l'ID de l'utilisateur via $_GET['id'].
// Note : on ne peut pas supprimer son propre compte (géré dans admin.php).
// ============================================================

session_start();
require 'config.php';

// ── 1. Vérification de la session ────────────────────────────
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.html");
    exit;
}

// ── 2. Vérification du rôle admin ───────────────────────────
$sql = $pdo->prepare("SELECT role FROM utilisateurs WHERE email = ?");
$sql->execute([$_SESSION['email']]);
$user = $sql->fetch();

if (!$user || $user['role'] !== 'admin') {
    die("⛔ Accès refusé");
}

// ── 3. Suppression de l'utilisateur ─────────────────────────
if (isset($_GET['id'])) {

    // Cast en entier pour neutraliser toute injection SQL
    $id = (int) $_GET['id'];

    $delete = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $delete->execute([$id]);

    // Retour au panel admin après suppression
    header("Location: admin.php");
    exit;
}
?>
