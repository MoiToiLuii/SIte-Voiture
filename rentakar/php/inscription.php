<?php
// ============================================================
// TRAITEMENT DE L'INSCRIPTION
// Vérifie que l'email n'existe pas déjà, hash le mot de passe,
// insère l'utilisateur, puis redirige vers le login.
// ============================================================

require 'config.php';

// On n'accepte que les requêtes POST (soumission de formulaire)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Nettoyage et récupération des champs
    $nom          = htmlspecialchars(trim($_POST['nom']));
    $email        = htmlspecialchars(trim($_POST['email']));
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hash bcrypt sécurisé

    // ── Vérification : l'email est-il déjà utilisé ? ─────────
    $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        // Email déjà pris → on retourne au formulaire avec un message d'erreur dans l'URL
        header("Location: ../pages/inscription.html?erreur=email_pris");
        exit;
    }

    // ── Insertion du nouvel utilisateur ──────────────────────
    // Le rôle est 'user' par défaut (défini dans la BDD)
    $sql = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
    $sql->execute([$nom, $email, $mot_de_passe]);

    // ── Redirection vers le login avec message de succès ─────
    header("Location: ../pages/login.html?succes=1");
    exit;
}
?>
