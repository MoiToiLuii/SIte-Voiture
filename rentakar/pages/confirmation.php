<?php
// ============================================================
// PAGE DE CONFIRMATION – confirmation.php
// Affiche le récapitulatif après une réservation réussie.
// Les données proviennent de $_SESSION['confirmation']
// (stockées par reservation.php juste avant la redirection).
// ============================================================

session_start();

// ── Protection : accès direct sans réservation ──────────────
// Si on arrive ici sans passer par reservation.php (pas de données en session),
// on redirige vers la page des locations.
if (!isset($_SESSION['confirmation'])) {
    header("Location: location.php");
    exit;
}

// ── Récupération des données de confirmation ─────────────────
$resa = $_SESSION['confirmation'];

// Suppression immédiate des données de session pour éviter
// de revoir la confirmation en rafraîchissant la page (F5)
unset($_SESSION['confirmation']);

// ── Formatage des dates en français (JJ/MM/AAAA) ─────────────
$date_debut_fr = date('d/m/Y', strtotime($resa['date_debut']));
$date_fin_fr   = date('d/m/Y', strtotime($resa['date_fin']));

// ── Correspondance clé assurance → libellé lisible ───────────
$assurances = [
    'tous-risques' => 'Tous risques',
    'tiers'        => 'Au tiers',
    'vol-incendie' => 'Vol et incendie',
];
$assurance_label = $assurances[$resa['assurance']] ?? $resa['assurance'];

// ── Correspondance clé véhicule → nom complet ────────────────
$noms_voitures = [
    'clio'    => 'Renault Clio',
    '208'     => 'Peugeot 208',
    'i20'     => 'Hyundai i20',
    'yaris'   => 'Toyota Yaris GR',
    'polo'    => 'Volkswagen Polo',
    'focus'   => 'Ford Focus',
    'classec' => 'Mercedes Classe C',
    'classee' => 'Mercedes Classe E',
    'serie3'  => 'BMW Série 3',
    'a4'      => 'Audi A4',
    'x1'      => 'BMW X1',
    'q5'      => 'Audi Q5',
    'p911'    => 'Porsche 911',
    'r8'      => 'Audi R8',
    'amggt'   => 'Mercedes AMG GT',
];
$nom_voiture = $noms_voitures[$resa['voiture']] ?? strtoupper($resa['voiture']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation – RentaKar</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ── Carte de confirmation centrée ── */
        .carte-confirmation {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            padding: 40px;
            text-align: center;
            border-top: 5px solid #2ecc71; /* Barre verte = succès */
        }

        /* Grande icône de succès */
        .icone-succes { font-size: 60px; margin-bottom: 15px; }

        /* Titre vert */
        .carte-confirmation h1 { color: #2ecc71; font-size: 26px; margin-bottom: 8px; }

        /* Sous-titre discret */
        .carte-confirmation .sous-titre { color: #888; font-size: 15px; margin-bottom: 30px; }

        /* Bloc récapitulatif sur fond gris clair */
        .recap { background: #f8f9fa; border-radius: 10px; padding: 20px 25px; text-align: left; margin-bottom: 25px; }
        .recap h2 { font-size: 16px; color: #1A3D63; margin-bottom: 15px; border-bottom: 1px solid #dee2e6; padding-bottom: 8px; }

        /* Ligne label / valeur dans le récapitulatif */
        .recap-ligne           { display: flex; justify-content: space-between; padding: 7px 0; font-size: 14px; border-bottom: 1px solid #f0f0f0; color: #333; }
        .recap-ligne:last-child{ border-bottom: none; }
        .recap-ligne .label    { color: #888; font-weight: 500; }
        .recap-ligne .valeur   { font-weight: 500; color: #1A3D63; }

        /* Badge numéro de réservation */
        .num-resa { background: #e8f5e9; color: #2e7d32; border-radius: 6px; padding: 2px 8px; font-weight: bold; }

        /* Boutons d'action */
        .boutons-action { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }

        .btn-accueil  { background: #4A7FA7; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; transition: 0.2s; }
        .btn-accueil:hover { background: #1A3D63; }

        .btn-mes-loc  { background: #2ecc71; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; transition: 0.2s; }
        .btn-mes-loc:hover { background: #27ae60; }

        .btn-nouvelle { background: #f0f0f0; color: #1A3D63; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; transition: 0.2s; }
        .btn-nouvelle:hover { background: #ddd; }
    </style>
    <style> header        { background-image: url("../images/Logo.png"); } </style>
</head>

<body>

<header>
    <h1>RentaKar</h1>
    <p class="slogan">Le meilleur ou rien</p>
  
</header>

<nav>
    <a href="../index.php">Accueil</a>
    <a href="location.php">Locations</a>
    <a href="mes_locations.php">Mes locations</a>
    <a href="../php/logout.php">Déconnexion</a>
</nav>

<main>
    <div class="carte-confirmation">

        <!-- Icône et titre de succès -->
        <div class="icone-succes">✅</div>
        <h1>Réservation confirmée !</h1>
        <p class="sous-titre">
            Merci <?= htmlspecialchars($resa['nom']) ?>, votre réservation a bien été enregistrée.
        </p>

        <!-- Récapitulatif détaillé de la réservation -->
        <div class="recap">
            <h2>📋 Récapitulatif</h2>

            <div class="recap-ligne">
                <span class="label">N° de réservation</span>
                <span class="valeur"><span class="num-resa">#<?= $resa['id'] ?></span></span>
            </div>

            <div class="recap-ligne">
                <span class="label">Véhicule</span>
                <span class="valeur"><?= htmlspecialchars($nom_voiture) ?></span>
            </div>

            <div class="recap-ligne">
                <span class="label">Nom</span>
                <span class="valeur"><?= htmlspecialchars($resa['nom']) ?></span>
            </div>

            <div class="recap-ligne">
                <span class="label">Email</span>
                <span class="valeur"><?= htmlspecialchars($resa['email']) ?></span>
            </div>

            <div class="recap-ligne">
                <span class="label">Date de début</span>
                <span class="valeur"><?= $date_debut_fr ?></span>
            </div>

            <div class="recap-ligne">
                <span class="label">Date de fin</span>
                <span class="valeur"><?= $date_fin_fr ?></span>
            </div>

            <div class="recap-ligne">
                <span class="label">Durée</span>
                <span class="valeur"><?= $resa['nb_jours'] ?> jour(s)</span>
            </div>

            <div class="recap-ligne">
                <span class="label">Assurance</span>
                <span class="valeur"><?= $assurance_label ?></span>
            </div>

        </div>

        <!-- Boutons de navigation post-réservation -->
        <div class="boutons-action">
            <a href="../index.php" class="btn-accueil">🏠 Accueil</a>
            <a href="mes_locations.php" class="btn-mes-loc">📋 Mes locations</a>
            <a href="location.php" class="btn-nouvelle">🚗 Nouvelle location</a>
        </div>

    </div>
</main>

<footer>
    <nav>
        <a href="faq.html">FAQ</a>
        <a href="mentions.html">Mentions légales</a>
        <a href="information.html">Divers information</a>
    </nav>
</footer>

</body>
</html>
