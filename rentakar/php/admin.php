<?php
// ============================================================
// PANEL ADMINISTRATEUR – admin.php
// Accessible uniquement aux utilisateurs avec role = 'admin'.
// Affiche : statistiques rapides + liste des réservations + liste des utilisateurs.
// ============================================================

session_start();
require 'config.php';

// ── 1. Vérification de la session ───────────────────────────
// Si l'utilisateur n'est pas connecté, retour au login
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.html");
    exit;
}

// ── 2. Vérification du rôle admin ───────────────────────────
// On vérifie en base (et pas seulement en session) pour plus de sécurité
$sql = $pdo->prepare("SELECT role FROM utilisateurs WHERE nom = ?");
$sql->execute([$_SESSION['user']]);
$user = $sql->fetch();

if (!$user || $user['role'] !== 'admin') {
    die("⛔ Accès refusé — vous n'avez pas les droits administrateur.");
}

// ── 3. Statistiques du tableau de bord ──────────────────────
$nb_users   = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$nb_resas   = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$nb_attente = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'")->fetchColumn();

// ── 4. Liste de tous les utilisateurs (les plus récents d'abord) ──
$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY cree_le DESC")->fetchAll();

// ── 5. Liste de toutes les réservations (les plus récentes d'abord) ──
$reservations = $pdo->query("SELECT * FROM reservations ORDER BY date_reservation DESC")->fetchAll();

// ── 6. Correspondance clé modèle → nom complet affiché ──────
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin – RentaKar</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ── Tableau de bord : 3 cartes de statistiques côte à côte ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        /* Carte de statistique individuelle */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px 25px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-top: 4px solid #4A7FA7;
        }

        /* Chiffre principal de la stat (ex: "42") */
        .stat-card .chiffre {
            font-size: 42px;
            font-weight: bold;
            color: #1A3D63;
            line-height: 1;
            margin-bottom: 6px;
        }

        /* Label sous le chiffre (ex: "Utilisateurs inscrits") */
        .stat-card .label-stat { font-size: 14px; color: #888; }

        /* Carte "en attente" en orange pour attirer l'attention */
        .stat-card.alerte             { border-top-color: #e67e22; }
        .stat-card.alerte .chiffre    { color: #e67e22; }

        /* Titre de section dans le panel */
        .section-titre {
            font-size: 20px;
            font-weight: bold;
            color: #0A1931;
            margin: 30px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #4A7FA7;
        }

        /* Tableau générique */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            font-size: 14px;
        }

        thead                    { background: #0A1931; color: white; }
        thead th                 { padding: 12px 15px; text-align: left; font-weight: 500; font-size: 13px; }
        tbody tr                 { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; }
        tbody tr:hover           { background: #f8f9ff; }
        tbody td                 { padding: 11px 15px; color: #333; vertical-align: middle; }

        /* Badges de statut de réservation (colorés selon l'état) */
        .badge-statut            { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-statut.en_attente { background: #fff3cd; color: #856404; }
        .badge-statut.confirmee  { background: #d1e7dd; color: #0a3622; }
        .badge-statut.annulee    { background: #f8d7da; color: #58151c; }

        /* Badges de rôle utilisateur */
        .badge-role              { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-role.admin        { background: #cfe2ff; color: #084298; }
        .badge-role.user         { background: #f0f0f0; color: #555; }

        /* Bouton de suppression rouge */
        .btn-supprimer           { background: #e74c3c; color: white; padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: 500; transition: 0.2s; }
        .btn-supprimer:hover     { background: #c0392b; }

        /* Message si un tableau est vide */
        .vide                    { text-align: center; color: #aaa; padding: 30px; font-size: 14px; }

        /* Responsive : sur mobile les cartes stats passent en colonne */
        @media (max-width: 700px) {
            .stats-grid           { grid-template-columns: 1fr; }
            table                 { font-size: 12px; }
            thead th, tbody td    { padding: 8px 10px; }
        }
    </style>
</head>

<body>

<header>
    <h1>RentaKar</h1>
    <p class="slogan">Le meilleur ou rien</p>
</header>

<nav>
    <a href="../index.php">Accueil</a>
    <a href="../pages/location.php">Locations</a>
    <!-- Nom de l'admin connecté affiché dans la nav -->
    <span style="color:#e67e22; font-weight:bold;">
        🔧 Admin : <?= htmlspecialchars($_SESSION['user']) ?>
    </span>
    <a href="logout.php">Déconnexion</a>
</nav>

<main style="max-width:1100px; margin:30px auto; padding:20px;">

    <h1 style="margin-bottom:25px;">Panel Administrateur</h1>

    <!-- ── Tableau de bord : 3 statistiques rapides ── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="chiffre"><?= $nb_users ?></div>
            <div class="label-stat">Utilisateurs inscrits</div>
        </div>
        <div class="stat-card">
            <div class="chiffre"><?= $nb_resas ?></div>
            <div class="label-stat">Réservations totales</div>
        </div>
        <!-- Carte orange si des réservations sont en attente de confirmation -->
        <div class="stat-card alerte">
            <div class="chiffre"><?= $nb_attente ?></div>
            <div class="label-stat">En attente de confirmation</div>
        </div>
    </div>

    <!-- ── Section : liste des réservations ── -->
    <div class="section-titre">📋 Réservations</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Véhicule</th>
                <th>Client</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Jours</th>
                <th>Assurance</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($reservations)): ?>
            <tr><td colspan="11" class="vide">Aucune réservation pour le moment.</td></tr>
        <?php else: ?>
            <?php foreach ($reservations as $r): ?>
                <?php
                    // Calcul durée en jours
                    $d1    = new DateTime($r['date_debut']);
                    $d2    = new DateTime($r['date_fin']);
                    $jours = $d2->diff($d1)->days;

                    // Formatage des dates en français (JJ/MM/AAAA)
                    $debut_fr = date('d/m/Y', strtotime($r['date_debut']));
                    $fin_fr   = date('d/m/Y', strtotime($r['date_fin']));

                    // Nom complet du véhicule depuis le tableau de correspondance
                    $nom_voiture = $noms_voitures[$r['voiture']] ?? strtoupper($r['voiture']);

                    // Libellé lisible de l'assurance
                    $assurances = [
                        'tous-risques' => 'Tous risques',
                        'tiers'        => 'Au tiers',
                        'vol-incendie' => 'Vol & incendie',
                    ];
                    $assurance_label = $assurances[$r['assurance']] ?? $r['assurance'];
                ?>
                <tr>
                    <td><strong>#<?= $r['id'] ?></strong></td>
                    <td><?= htmlspecialchars($nom_voiture) ?></td>
                    <td><?= htmlspecialchars($r['nom']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['telephone']) ?></td>
                    <td><?= $debut_fr ?></td>
                    <td><?= $fin_fr ?></td>
                    <td><?= $jours ?> j</td>
                    <td><?= $assurance_label ?></td>
                    <td>
                        <!-- Badge coloré selon le statut de la réservation -->
                        <span class="badge-statut <?= $r['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $r['statut'])) ?>
                        </span>
                    </td>
                    <td>
                        <!-- Lien de suppression avec confirmation JS -->
                        <a class="btn-supprimer"
                           href="delete_reservation.php?id=<?= (int)$r['id'] ?>"
                           onclick="return confirm('Supprimer la réservation #<?= $r['id'] ?> ?')">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- ── Section : liste des utilisateurs ── -->
    <div class="section-titre" style="margin-top:40px;">👥 Utilisateurs</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Inscrit le</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="6" class="vide">Aucun utilisateur.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong>#<?= $u['id'] ?></strong></td>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <!-- Badge coloré selon le rôle (admin en bleu, user en gris) -->
                        <span class="badge-role <?= $u['role'] ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['cree_le'])) ?></td>
                    <td>
                        <?php if ($u['nom'] !== $_SESSION['user']): ?>
                            <!-- Bouton supprimer — désactivé pour l'admin connecté (on ne peut pas se supprimer soi-même) -->
                            <a class="btn-supprimer"
                               href="delete_user.php?id=<?= (int)$u['id'] ?>"
                               onclick="return confirm('Supprimer l\'utilisateur <?= htmlspecialchars($u['nom']) ?> ?')">
                                Supprimer
                            </a>
                        <?php else: ?>
                            <span style="color:#aaa; font-size:12px;">Vous</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

</main>

<footer>
    <nav>
        <a href="../pages/faq.html">FAQ</a>
        <a href="../pages/mentions.html">Mentions légales</a>
        <a href="../pages/information.html">Divers information</a>
    </nav>
</footer>

</body>
</html>
