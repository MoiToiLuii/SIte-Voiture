<?php
// ============================================================
// CONFIGURATION BASE DE DONNÉES – config.php
// Ce fichier est inclus par tous les scripts PHP qui ont
// besoin de se connecter à MySQL via PDO.
// ============================================================

// ── Paramètres de connexion ──────────────────────────────────
// À adapter selon votre environnement (XAMPP, hébergeur, etc.)
$host   = "localhost";      // Serveur MySQL (localhost sur XAMPP)
$dbname = "Rentakar";       // Nom de la base (créée par bdd.sql)
$username = "root";         // Utilisateur MySQL (root par défaut sur XAMPP)
$password = "";             // Mot de passe MySQL (vide par défaut sur XAMPP)

// ── Connexion PDO ────────────────────────────────────────────
// PDO est plus sécurisé que mysqli : il supporte les requêtes préparées
// et gère plusieurs types de bases de données.
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // ERRMODE_EXCEPTION : toute erreur SQL lance une exception PHP
    // → on peut la capturer avec try/catch pour afficher un message propre
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // FETCH_ASSOC : par défaut, fetch() retourne un tableau associatif
    // (accès par nom de colonne) plutôt qu'un tableau indexé
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En cas d'échec de connexion, on affiche un message d'erreur
    // En production, il faudrait logger l'erreur et ne PAS afficher $e->getMessage()
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
