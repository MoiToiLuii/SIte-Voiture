<?php
// ============================================================
// PAGE DIVERS INFORMATIONS – information.php
// Protégée par session : l'utilisateur doit être connecté.
// Affiche les informations diverses sur le site.
// ============================================================

session_start();

// ── Protection : si non connecté, retour au login ────────────
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

// Nom de l'utilisateur connecté (affiché dans la nav)
$nom_utilisateur = htmlspecialchars($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Divers informations – RentaKar</title>
    <!-- Feuille de style principale -->
    <link rel="stylesheet" href="../css/style.css">
    <style> header { background-image: url("../images/Logo.png"); } </style>
</head>

<body>

<!-- En-tête de la page -->
<header>
    <h1>Divers informations</h1>
</header>

<!-- Barre de navigation principale -->
<nav>
    <a href="../index.php">Accueil</a>
    <a href="location.php">Locations</a>
    <a href="mes_locations.php">Mes locations</a>
    <span style="color:#4A7FA7; font-weight:bold;">👤 <?= $nom_utilisateur ?></span>
    <a href="../php/logout.php">Déconnexion</a>
</nav>

<!-- Contenu principal de la page -->
<main style="max-width:900px; margin:40px auto; padding:20px;">

    <!-- Section : présentation générale du projet -->
    <h2>À propos du site</h2>
    <p>
        Ce site a été réalisé dans le cadre d'un projet étudiant.
        Il a pour objectif de présenter différents véhicules et de simuler un service de location.
    </p>

    <!-- Section : objectifs et mission du site -->
    <h2>Notre mission</h2>
    <p>
        Offrir une interface simple, intuitive et agréable permettant aux utilisateurs de consulter des véhicules,
        lire leurs caractéristiques et comprendre le fonctionnement d'un service de location automobile.
    </p>

    <!-- Section : origine et droits des images utilisées -->
    <h2>Sources des images</h2>
    <p>
        Les images utilisées proviennent de banques d'images libres de droits ou sont utilisées dans un cadre pédagogique.
    </p>

    <!-- Section : description des différentes pages du site -->
    <h2>Fonctionnement du site</h2>
    <p>
        Le site est composé de plusieurs pages :
        <ul>
            <li><strong>Accueil</strong> – Présentation générale</li>
            <li><strong>Locations</strong> – Détails et descriptifs des voitures</li>
            <li><strong>FAQ</strong> – Questions fréquentes</li>
            <li><strong>Mentions légales</strong> – Informations obligatoires</li>
        </ul>
    </p>

    <!-- Section : informations de contact du projet -->
    <h2>Contact</h2>
    <p>
        Pour toute question concernant le projet, vous pouvez nous contacter à :
        <strong>contact@sitevoiture.fr</strong>
    </p>

</main>

<!-- Footer -->
<footer>
    <nav>
        <a href="faq.php">FAQ</a>
        <a href="mentions.html">Mentions légales</a>
        <a href="information.php">Divers information</a>
    </nav>
</footer>

</body>
</html>
