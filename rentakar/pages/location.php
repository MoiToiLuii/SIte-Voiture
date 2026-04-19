<?php
// ============================================================
// PAGE LOCATIONS – location.php
// Protégée par session : l'utilisateur doit être connecté.
// Affiche la grille de tous les véhicules disponibles.
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
    <title>RentaKar – Locations</title>
    <link rel="stylesheet" href="../css/style.css">
    <style> header        { background-image: url("../images/Logo.png"); } </style>
</head>

<body>

<header>
    <h1>RentaKar</h1>
    <p class="slogan">Le meilleur ou rien</p>
    
</header>

<!-- ── Navigation avec liens personnalisés ── -->
<nav>
    <a href="../index.php">Accueil</a>
    <a href="location.php">Locations</a>
    <a href="mes_locations.php">Mes locations</a>
    <span style="color:#4A7FA7; font-weight:bold;">👤 <?= $nom_utilisateur ?></span>
    <a href="../php/logout.php">Déconnexion</a>
</nav>

<main>

    <!-- ── Popup descriptif voiture ── -->
    <!--
        Affiché quand l'utilisateur clique sur "Descriptif".
        Le titre (id="titre-descriptif") et le texte (id="texte-descriptif")
        sont remplis dynamiquement par la fonction ouvrirPopup() dans script.js.
    -->
    <div id="popup-descriptif" class="popup">
        <div class="popup-content modern-popup">
            <span class="close" onclick="fermerPopup()">&times;</span>
            <h2 id="titre-descriptif"></h2>
            <p id="texte-descriptif"></p>
        </div>
    </div>

    <!-- ── Grille de véhicules ── -->
    <!--
        Chaque .carte représente un modèle de voiture.
        - Le src des images utilise un chemin relatif depuis la racine XAMPP.
        - onclick="ouvrirPopup('cle')" → affiche le descriptif dans le popup
        - onclick="ouvrirPopupLocation('cle')" → ouvre le formulaire de réservation
    -->
    <section class="grid-voitures">

        <div class="carte">
            <img src="../images/clio.jpg" alt="Renault Clio">
            <h3>Renault Clio</h3>
            <button onclick="ouvrirPopup('clio')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('clio')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/208.jpg" alt="Peugeot 208">
            <h3>Peugeot 208</h3>
            <button onclick="ouvrirPopup('208')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('208')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/Hyundai-i20.png" alt="Hyundai i20">
            <h3>Hyundai i20</h3>
            <button onclick="ouvrirPopup('i20')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('i20')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/Yaris.jpg" alt="Toyota Yaris">
            <h3>Toyota Yaris GR</h3>
            <button onclick="ouvrirPopup('yaris')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('yaris')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/vwpolo.png" alt="Volkswagen Polo">
            <h3>Volkswagen Polo</h3>
            <button onclick="ouvrirPopup('polo')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('polo')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/ford-focus.jpg" alt="Ford Focus">
            <h3>Ford Focus</h3>
            <button onclick="ouvrirPopup('focus')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('focus')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/classe-c.jpg" alt="Mercedes Classe C">
            <h3>Mercedes Classe C</h3>
            <button onclick="ouvrirPopup('classec')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('classec')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/classe-e.jpg" alt="Mercedes Classe E">
            <h3>Mercedes Classe E</h3>
            <button onclick="ouvrirPopup('classee')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('classee')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/bmw-serie3.jpg" alt="BMW Série 3">
            <h3>BMW Série 3</h3>
            <button onclick="ouvrirPopup('serie3')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('serie3')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/audi-a4.jpg" alt="Audi A4">
            <h3>Audi A4</h3>
            <button onclick="ouvrirPopup('a4')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('a4')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/bmw-x1.jpg" alt="BMW X1">
            <h3>BMW X1</h3>
            <button onclick="ouvrirPopup('x1')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('x1')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/audi-q5.jpg" alt="Audi Q5">
            <h3>Audi Q5</h3>
            <button onclick="ouvrirPopup('q5')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('q5')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/porsche-911.jpg" alt="Porsche 911">
            <h3>Porsche 911</h3>
            <button onclick="ouvrirPopup('p911')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('p911')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/audi-r8.jpg" alt="Audi R8">
            <h3>Audi R8</h3>
            <button onclick="ouvrirPopup('r8')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('r8')">Louer</button>
        </div>

        <div class="carte">
            <img src="../images/mercedes-amg-gt.jpg" alt="Mercedes AMG GT">
            <h3>Mercedes AMG GT</h3>
            <button onclick="ouvrirPopup('amggt')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirPopupLocation('amggt')">Louer</button>
        </div>

    </section>

</main>

<!-- ── Popup formulaire de location ── -->
<!--
    Affiché quand l'utilisateur clique sur "Louer".
    Le champ caché "voiture" est rempli par ouvrirPopupLocation() dans script.js.
    Le formulaire soumet vers reservation.php qui insère en BDD.
-->
<div id="popup-location" class="popup">
    <div class="popup-content modern-popup">

        <span class="close" onclick="fermerPopupLocation()">&times;</span>
        <h2>Location : <span id="modele-location"></span></h2>

        <form method="POST" action="../php/reservation.php" class="form-modern">

            <!-- Champ caché : contient la clé du modèle (ex: 'clio') -->
            <input type="hidden" name="voiture" id="voiture">

            <div class="form-group">
                <label>Nom et Prénom :</label>
                <input type="text" name="nom" required>
            </div>

            <div class="form-group">
                <label>Email :</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Téléphone :</label>
                <input type="tel" name="telephone" required>
            </div>

            <div class="form-group">
                <label>Date de début :</label>
                <!-- L'attribut min est défini par JS pour bloquer les dates passées -->
                <input type="date" name="date_debut" id="date_debut" required>
            </div>

            <div class="form-group">
                <label>Date de fin :</label>
                <input type="date" name="date_fin" id="date_fin" required>
            </div>

            <!-- Récapitulatif durée : affiché dynamiquement par JS quand les deux dates sont remplies -->
            <div id="recap-prix" style="
                display:none;
                background:#f0f7ff;
                border:1px solid #4A7FA7;
                border-radius:8px;
                padding:10px 14px;
                font-size:14px;
                color:#1A3D63;
                margin-bottom:8px;
            ">
                📅 Durée : <strong id="nb-jours">0</strong> jour(s)
            </div>

            <div class="form-group">
                <label>Assurance :</label>
                <select name="assurance" required>
                    <option value="">Choisir une assurance</option>
                    <option value="tous-risques">Tous risques</option>
                    <option value="tiers">Au tiers</option>
                    <option value="vol-incendie">Vol et incendie</option>
                </select>
            </div>

            <button type="submit" class="btn-green">Confirmer la location</button>

        </form>
    </div>
</div>

<footer>
    <nav>
        <a href="faq.html">FAQ</a>
        <a href="mentions.html">Mentions légales</a>
        <a href="information.html">Divers information</a>
    </nav>
</footer>

<script src="../js/script.js"></script>

<script>
    // ============================================================
    // CALCUL DE LA DURÉE ET BLOCAGE DES DATES PASSÉES
    // ============================================================

    // Empêche de choisir une date de début dans le passé
    const aujourd_hui = new Date().toISOString().split('T')[0];
    document.getElementById('date_debut').min = aujourd_hui;
    document.getElementById('date_fin').min   = aujourd_hui;

    // Quand la date de début change → met à jour le min de date_fin et recalcule
    document.getElementById('date_debut').addEventListener('change', function () {
        document.getElementById('date_fin').min = this.value;
        calculerJours();
    });

    // Quand la date de fin change → recalcule la durée
    document.getElementById('date_fin').addEventListener('change', calculerJours);

    // Calcule et affiche le nombre de jours entre les deux dates sélectionnées
    function calculerJours() {
        const debut = document.getElementById('date_debut').value;
        const fin   = document.getElementById('date_fin').value;

        if (!debut || !fin) return;

        const diffMs    = new Date(fin) - new Date(debut);
        const diffJours = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
        const recap     = document.getElementById('recap-prix');

        if (diffJours > 0) {
            document.getElementById('nb-jours').textContent = diffJours;
            recap.style.display = 'block';
        } else {
            // Date de fin avant ou égale à la date de début : on masque
            recap.style.display = 'none';
        }
    }
</script>

</body>
</html>
