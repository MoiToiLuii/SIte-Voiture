<?php
// ============================================================
// PAGE D'ACCUEIL – index.php
// Remplace index.html pour pouvoir lire la session PHP.
// Si l'utilisateur est connecté, on personnalise la nav.
// ============================================================

session_start();

// Vérifie si l'utilisateur est connecté
$connecte       = isset($_SESSION['user']);
$nom_utilisateur = $connecte ? htmlspecialchars($_SESSION['user']) : null;
$est_admin      = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>RentaKar – Accueil</title>
    <link rel="stylesheet" href="css/style.css">

    <style> header        { background-image: url("images/Logo.png"); }
            .hero         { background: url("images/fond_ecran_accueil.jpeg") center/cover no-repeat; }
            .intro-image  { background-image: url("images/2.png"); }
    </style>
</head>

<body>

<!-- ── En-tête principal ── -->
<header>
    <h1>RentaKar</h1>
    <p class="slogan">Le meilleur ou rien</p>
</header>

<!-- ── Navigation adaptée selon la connexion ── -->
<nav>
    <a href="index.php">Accueil</a>
    <a href="pages/location.php">Locations</a>

    <?php if ($connecte): ?>
        <!-- Utilisateur connecté : on affiche son nom, ses locations et la déconnexion -->
        <a href="pages/mes_locations.php">Mes locations</a>

        <?php if ($est_admin): ?>
            <!-- Lien panel admin uniquement pour les admins -->
            <a href="php/admin.php" style="color:#e67e22; font-weight:bold;">🔧 Admin</a>
        <?php endif; ?>

        <span style="color:#4A7FA7; font-weight:bold;">👤 <?= $nom_utilisateur ?></span>
        <a href="php/logout.php">Déconnexion</a>

    <?php else: ?>
        <!-- Visiteur non connecté : lien connexion -->
        <a href="pages/login.html">Connexion</a>
    <?php endif; ?>
</nav>

<!-- ── Section Hero (grande bannière d'accueil) ── -->
<section class="hero">
    <!--
        Bannière visuelle pleine largeur.
        L'image de fond est définie dans .hero via style.css.
        L'overlay semi-transparent (::after) améliore la lisibilité du texte.
    -->
    <h1>Trouver et louer votre voiture <br>en toute simplicité</h1>
</section>

<!-- ── Contenu principal ── -->
<main>

    <!-- Section intro : image à gauche, texte à droite -->
    <section class="intro-section">
        <!--
            Mise en page deux colonnes via flexbox.
            .intro-image : background-image défini en CSS (images/2.png)
            .intro-texte : texte de présentation à droite
        -->

        <div class="intro-image"></div>

        <div class="intro-texte">
            <h2>Bienvenue sur RentaKar</h2>
            <p>
                Découvrez notre catalogue de véhicules : citadines, berlines premium, SUV et supercars.
                Chaque voiture est décrite en détail pour vous aider à faire le bon choix.
            </p>
            <p>
                La réservation se fait en quelques clics, directement depuis la page
                <a href="pages/location.php" style="color:#4A7FA7; font-weight:bold;">Locations</a>.
            </p>

            <?php if (!$connecte): ?>
                <!-- Bouton d'appel à l'action pour les visiteurs non connectés -->
                <a href="pages/login.html" class="bouton" style="margin-top:10px; display:inline-block;">
                    🚗 Commencer maintenant
                </a>
            <?php else: ?>
                <!-- Utilisateur déjà connecté : on l'invite directement à louer -->
                <a href="pages/location.php" class="bouton" style="margin-top:10px; display:inline-block;">
                    🚗 Voir les véhicules
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section : explication du fonctionnement -->
    <section>
        <h3>Comment ça fonctionne ?</h3>
        <p>
            Parcourez notre catalogue, consultez le descriptif de chaque véhicule,
            puis cliquez sur "Louer" pour soumettre votre demande de réservation.
        </p>
        <p>
            La navigation est simple et intuitive :
            sélection du véhicule → choix des dates → confirmation de réservation.
        </p>
    </section>

    <!-- Section : contexte scolaire du projet -->
    <section>
        <h3>Projet étudiant</h3>
        <p>
            Ce site est réalisé dans le cadre d'une formation en développement web à Paris Nanterre.
            Il met en pratique HTML, CSS, JavaScript et PHP/MySQL.
        </p>
        <p>Les informations présentées sont données à titre d'exemple.</p>
    </section>

    <!-- Section avis : remplie dynamiquement par script.js -->
    <section class="avis">
        <h2>Avis clients</h2>
        <!--
            Ce div est vide au chargement.
            Le fichier js/script.js le remplit avec des cartes d'avis animées.
            Les avis défilent toutes les 4 secondes (setInterval).
        -->
        <div id="avis-dynamique"></div>
    </section>

</main>

<!-- ── Pied de page ── -->
<footer>
    <nav>
        <a href="pages/faq.html">FAQ</a>
        <a href="pages/mentions.html">Mentions légales</a>
        <a href="pages/information.html">Divers information</a>
    </nav>
</footer>

<script src="js/script.js"></script>

</body>
</html>
