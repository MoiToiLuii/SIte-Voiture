<?php
// ============================================================
// SUPPRESSION D'UNE RÉSERVATION – delete_reservation.php
// Accessible uniquement depuis le panel admin (role = 'admin').
// Reçoit l'ID de la réservation via $_GET['id'].
// ============================================================

session_start();
require 'config.php';

// ── 1. Vérification que l'utilisateur est bien admin ─────────
$sql = $pdo->prepare("SELECT role FROM utilisateurs WHERE nom = ?");
$sql->execute([$_SESSION['user']]);
$user = $sql->fetch();

if (!$user || $user['role'] !== 'admin') {
    // Accès refusé : l'utilisateur n'est pas admin
    die("⛔ Accès refusé");
}

// ── 2. Suppression de la réservation ────────────────────────
if (isset($_GET['id'])) {

    // Cast en entier pour neutraliser toute tentative d'injection SQL
    // Ex : ?id=1 OR 1=1 devient simplement id=1
    $id = (int) $_GET['id'];

    $delete = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $delete->execute([$id]);

    // Retour au panel admin après suppression
    header("Location: admin.php");
    exit;
}
?>
