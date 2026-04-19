<?php
// ============================================================
// CONNEXION UTILISATEUR – login.php
// Vérifie email + mot de passe, crée la session si OK,
// puis redirige selon le rôle (user → locations, admin → panel).
// ============================================================

session_start();
require 'config.php';

// On n'accepte que les soumissions de formulaire (méthode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ── Récupération des champs du formulaire ────────────────
    $email        = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // ── Recherche de l'utilisateur par email ─────────────────
    // Requête préparée → protection contre les injections SQL
    $sql = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $sql->execute([$email]);
    $user = $sql->fetch();

    // ── Vérification du mot de passe ─────────────────────────
    // password_verify compare le mot de passe saisi avec le hash bcrypt stocké
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {

        // ── Création des variables de session ────────────────
        $_SESSION['user']  = $user['nom'];   // Nom affiché dans la nav
        $_SESSION['email'] = $user['email']; // Utilisé pour filtrer les réservations (mes_locations.php)
        $_SESSION['role']  = $user['role'];  // 'user' ou 'admin' — contrôle les accès

        // ── Redirection selon le rôle ─────────────────────────
        if ($user['role'] === 'admin') {
            header("Location: ../php/admin.php"); // Panel d'administration
        } else {
            header("Location: ../pages/location.php"); // Catalogue de véhicules
        }
        exit;

    } else {
        // ── Mauvais identifiants → retour au formulaire avec erreur ──
        header("Location: ../pages/login.html?erreur=1");
        exit;
    }
}
?>
