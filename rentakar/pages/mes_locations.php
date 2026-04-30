<?php
// ============================================================
// MES LOCATIONS
// Affiche toutes les réservations de l'utilisateur connecté.
// Accessible uniquement si la session est active.
// ============================================================

session_start();
require '../php/config.php';

// ── 1. Protection par session ────────────────────────────────
// Si l'utilisateur n'est pas connecté, on le renvoie au login
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

$nom_utilisateur = htmlspecialchars($_SESSION['user']);
$email_utilisateur = $_SESSION['email'] ?? '';

// ── 2. Récupération des réservations de cet utilisateur ──────
// On filtre par email (stocké en session lors du login)
$stmt = $pdo->prepare("
    SELECT * FROM reservations
    WHERE email = ?
    ORDER BY date_reservation DESC
");
$stmt->execute([$email_utilisateur]);
$reservations = $stmt->fetchAll();

// ── 3. Tableau de correspondance modèle → nom complet ────────
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

// ── 4. Tableau de correspondance assurance → libellé ─────────
$assurances = [
    'tous-risques'  => 'Tous risques',
    'tiers'         => 'Au tiers',
    'vol-incendie'  => 'Vol & incendie',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes locations – RentaKar</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ── Styles spécifiques à la page Mes locations ── */

        /* Conteneur principal centré */
        .container {
            max-width: 960px;
            margin: 40px auto;
            padding: 20px;
        }

        /* En-tête de page avec titre et compteur */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .page-header h1 {
            font-size: 26px;
            color: #0A1931;
            margin: 0;
        }

        /* Badge compteur de réservations */
        .badge-count {
            background: #4A7FA7;
            color: white;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        /* Carte de réservation individuelle */
        .resa-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 25px 30px;
            margin-bottom: 20px;
            border-left: 5px solid #4A7FA7;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .resa-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }

        /* Couleur de la bordure gauche selon le statut */
        .resa-card.confirmee  { border-left-color: #2ecc71; }
        .resa-card.en_attente { border-left-color: #e67e22; }
        .resa-card.annulee    { border-left-color: #e74c3c; }

        /* En-tête de la carte : nom du véhicule + badge statut */
        .resa-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .resa-header h2 {
            font-size: 18px;
            color: #0A1931;
            margin: 0;
        }

        /* Numéro de réservation discret */
        .resa-header .num {
            font-size: 13px;
            color: #aaa;
            font-weight: normal;
        }

        /* Grille des détails de la réservation */
        .resa-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
        }

        /* Bloc individuel d'un détail */
        .detail-bloc {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 14px;
        }

        /* Label du détail (ex: "Date de début") */
        .detail-bloc .d-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        /* Valeur du détail */
        .detail-bloc .d-value {
            font-size: 14px;
            font-weight: bold;
            color: #1A3D63;
        }

        /* Badges de statut colorés */
        .badge-statut {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-statut.en_attente { background: #fff3cd; color: #856404; }
        .badge-statut.confirmee  { background: #d1e7dd; color: #0a3622; }
        .badge-statut.annulee    { background: #f8d7da; color: #58151c; }

        /* Message si aucune réservation */
        .vide-message {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .vide-message .icone {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .vide-message h2 {
            color: #1A3D63;
            margin-bottom: 10px;
        }

        .vide-message p {
            color: #888;
            margin-bottom: 20px;
        }

        /* Responsive : sur mobile, les détails passent en 2 colonnes */
        @media (max-width: 600px) {
            .resa-details { grid-template-columns: repeat(2, 1fr); }
            .resa-card { padding: 18px; }
        }
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
    <a href="mes_locations.php" style="border-bottom: 2px solid #4A7FA7; padding-bottom:3px;">Mes locations</a>
    <span style="color:#4A7FA7; font-weight:bold;">👤 <?= $nom_utilisateur ?></span>
    <a href="../php/logout.php">Déconnexion</a>
</nav>

<main>
    <div class="container">

        <!-- ── En-tête de page ── -->
        <div class="page-header">
            <h1>🚗 Mes locations</h1>
            <span class="badge-count"><?= count($reservations) ?> réservation<?= count($reservations) > 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($reservations)): ?>

            <!-- ── Message si aucune réservation ── -->
            <div class="vide-message">
                <div class="icone">🅿️</div>
                <h2>Aucune réservation pour le moment</h2>
                <p>Vous n'avez pas encore loué de véhicule. Parcourez notre catalogue et trouvez la voiture idéale !</p>
                <a href="location.php" class="bouton">Voir les véhicules</a>
            </div>

        <?php else: ?>

            <!-- ── Liste des réservations ── -->
            <?php foreach ($reservations as $r): ?>
                <?php
                    // Calcul de la durée en jours
                    $d1    = new DateTime($r['date_debut']);
                    $d2    = new DateTime($r['date_fin']);
                    $jours = $d2->diff($d1)->days;

                    // Formatage des dates en français
                    $debut_fr = date('d/m/Y', strtotime($r['date_debut']));
                    $fin_fr   = date('d/m/Y', strtotime($r['date_fin']));

                    // Nom complet du véhicule
                    $nom_voiture = $noms_voitures[$r['voiture']] ?? strtoupper($r['voiture']);

                    // Libellé de l'assurance
                    $assurance_label = $assurances[$r['assurance']] ?? $r['assurance'];
                ?>
                <!-- Carte d'une réservation — classe CSS selon le statut pour la couleur de bordure -->
                <div class="resa-card <?= $r['statut'] ?>">

                    <!-- En-tête : nom du véhicule + statut -->
                    <div class="resa-header">
                        <h2>
                            <?= htmlspecialchars($nom_voiture) ?>
                            <span class="num">— Réservation #<?= $r['id'] ?></span>
                        </h2>
                        <span class="badge-statut <?= $r['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $r['statut'])) ?>
                        </span>
                    </div>

                    <!-- Grille des détails -->
                    <div class="resa-details">

                        <div class="detail-bloc">
                            <div class="d-label">📅 Date de début</div>
                            <div class="d-value"><?= $debut_fr ?></div>
                        </div>

                        <div class="detail-bloc">
                            <div class="d-label">📅 Date de fin</div>
                            <div class="d-value"><?= $fin_fr ?></div>
                        </div>

                        <div class="detail-bloc">
                            <div class="d-label">⏱ Durée</div>
                            <div class="d-value"><?= $jours ?> jour<?= $jours > 1 ? 's' : '' ?></div>
                        </div>

                        <div class="detail-bloc">
                            <div class="d-label">🛡 Assurance</div>
                            <div class="d-value"><?= $assurance_label ?></div>
                        </div>

                        <div class="detail-bloc">
                            <div class="d-label">📞 Téléphone</div>
                            <div class="d-value"><?= htmlspecialchars($r['telephone']) ?></div>
                        </div>

                        <div class="detail-bloc">
                            <div class="d-label">🕐 Réservé le</div>
                            <div class="d-value"><?= date('d/m/Y', strtotime($r['date_reservation'])) ?></div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

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
