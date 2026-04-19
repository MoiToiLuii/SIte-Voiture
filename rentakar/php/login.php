<?php
// ============================================================
// CONNEXION UTILISATEUR – login.php
// ============================================================

session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email        = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Requête préparée → protection SQL
    $sql = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $sql->execute([$email]);
    $user = $sql->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {

        // Crée la session
        $_SESSION['user']  = $user['nom'];
        $_SESSION['email'] = $user['email'];  // ← utilisé pour admin + mes_locations
        $_SESSION['role']  = $user['role'];

        // Génère un token CSRF à la connexion
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header("Location: ../php/admin.php");
        } else {
            header("Location: ../pages/location.php");
        }
        exit;

    } else {
        header("Location: ../pages/login.html?erreur=1");
        exit;
    }
}
?>
