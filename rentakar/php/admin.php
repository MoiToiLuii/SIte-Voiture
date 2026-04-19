<?php
// ============================================================
// PANEL ADMINISTRATEUR – admin.php
// Accessible uniquement aux utilisateurs avec role = 'admin'.
// ============================================================

session_start();
require 'config.php';

// ── 1. Vérification de la session ───────────────────────────
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.html");
    exit;
}

// ── 2. Vérification du rôle admin ───────────────────────────
// On vérifie par EMAIL (plus fiable que par nom qui peut être dupliqué)
$sql = $pdo->prepare("SELECT role FROM utilisateurs WHERE email = ?");
$sql->execute([$_SESSION['email']]);
$user = $sql->fetch();

if (!$user || $user['role'] !== 'admin') {
    die("⛔ Accès refusé — vous n'avez pas les droits administrateur.");
}

// ── 3. Traitement des actions POST (changement de statut) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id     = (int) ($_POST['id'] ?? 0);

    if ($action === 'changer_statut' && $id > 0) {
        $nouveau_statut = $_POST['statut'] ?? '';
        if (in_array($nouveau_statut, ['en_attente', 'confirmee', 'annulee'])) {
            $upd = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
            $upd->execute([$nouveau_statut, $id]);
        }
    }

    header("Location: admin.php");
    exit;
}

// ── 4. Statistiques ─────────────────────────────────────────
$nb_users    = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$nb_resas    = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$nb_attente  = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'")->fetchColumn();
$nb_confirmee = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'confirmee'")->fetchColumn();

// ── 5. Liste des utilisateurs ────────────────────────────────
$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY cree_le DESC")->fetchAll();

// ── 6. Liste des réservations ────────────────────────────────
$reservations = $pdo->query("SELECT * FROM reservations ORDER BY date_reservation DESC")->fetchAll();

// ── 7. Correspondances ───────────────────────────────────────
$noms_voitures = [
    'clio'=>'Renault Clio','208'=>'Peugeot 208','i20'=>'Hyundai i20',
    'yaris'=>'Toyota Yaris GR','polo'=>'Volkswagen Polo','focus'=>'Ford Focus',
    'classec'=>'Mercedes Classe C','classee'=>'Mercedes Classe E','serie3'=>'BMW Série 3',
    'a4'=>'Audi A4','x1'=>'BMW X1','q5'=>'Audi Q5',
    'p911'=>'Porsche 911','r8'=>'Audi R8','amggt'=>'Mercedes AMG GT',
];

$tarifs = [
    'clio'=>45,'208'=>50,'i20'=>48,'yaris'=>47,'polo'=>52,'focus'=>55,
    'classec'=>90,'classee'=>110,'serie3'=>95,'a4'=>100,'x1'=>120,
    'q5'=>130,'p911'=>350,'r8'=>500,'amggt'=>600
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin – RentaKar</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 18px 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-top: 4px solid #4A7FA7;
        }
        .stat-card .chiffre { font-size: 38px; font-weight: bold; color: #1A3D63; line-height:1; margin-bottom:5px; }
        .stat-card .label-stat { font-size: 13px; color: #888; }
        .stat-card.alerte  { border-top-color: #e67e22; }
        .stat-card.alerte .chiffre  { color: #e67e22; }
        .stat-card.success { border-top-color: #2ecc71; }
        .stat-card.success .chiffre { color: #27ae60; }

        .section-titre {
            font-size: 18px; font-weight: bold; color: #0A1931;
            margin: 28px 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #4A7FA7;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Onglets filtres */
        .filtres {
            display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;
        }
        .filtre-btn {
            padding: 5px 14px; border-radius: 20px; font-size: 13px;
            border: 1px solid #ddd; background: white; cursor: pointer;
            font-weight: 500; transition: 0.2s;
        }
        .filtre-btn:hover, .filtre-btn.actif { background: #0A1931; color: white; border-color: #0A1931; }

        table { width:100%; border-collapse:collapse; background:white;
                border-radius:10px; overflow:hidden;
                box-shadow:0 4px 12px rgba(0,0,0,0.07); font-size:13.5px; }
        thead { background:#0A1931; color:white; }
        thead th { padding:11px 12px; text-align:left; font-weight:500; }
        tbody tr { border-bottom:1px solid #f0f0f0; transition:background 0.15s; }
        tbody tr:hover { background:#f8f9ff; }
        tbody td { padding:10px 12px; color:#333; vertical-align:middle; }

        .badge-statut { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
        .badge-statut.en_attente { background:#fff3cd; color:#856404; }
        .badge-statut.confirmee  { background:#d1e7dd; color:#0a3622; }
        .badge-statut.annulee    { background:#f8d7da; color:#58151c; }

        .badge-role { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; }
        .badge-role.admin { background:#cfe2ff; color:#084298; }
        .badge-role.user  { background:#f0f0f0; color:#555; }

        .btn-supprimer { background:#e74c3c; color:white; padding:4px 10px; border-radius:5px; text-decoration:none; font-size:12px; transition:0.2s; }
        .btn-supprimer:hover { background:#c0392b; }

        /* Dropdown statut */
        .select-statut {
            padding: 3px 8px; border-radius: 5px; border: 1px solid #ddd;
            font-size: 12px; cursor: pointer; background: white;
        }
        .btn-sauver {
            background: #4A7FA7; color: white; padding: 4px 10px;
            border-radius: 5px; border: none; cursor: pointer; font-size: 12px;
            transition: 0.2s;
        }
        .btn-sauver:hover { background: #1A3D63; }

        .vide { text-align:center; color:#aaa; padding:30px; font-size:14px; }

        /* Recherche */
        .search-bar {
            width: 100%; padding: 9px 14px; border: 1px solid #ddd;
            border-radius: 8px; font-size: 14px; margin-bottom: 12px;
            outline: none; transition: 0.2s;
        }
        .search-bar:focus { border-color: #4A7FA7; }

        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            table { font-size: 12px; }
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
    <span style="color:#e67e22; font-weight:bold;">🔧 Admin : <?= htmlspecialchars($_SESSION['user']) ?></span>
    <a href="logout.php">Déconnexion</a>
</nav>

<main style="max-width:1200px; margin:30px auto; padding:20px;">

    <h1 style="margin-bottom:22px; color:#0A1931;">⚙️ Panel Administrateur</h1>

    <!-- ── 4 stats ── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="chiffre"><?= $nb_users ?></div>
            <div class="label-stat">👥 Utilisateurs</div>
        </div>
        <div class="stat-card">
            <div class="chiffre"><?= $nb_resas ?></div>
            <div class="label-stat">📋 Réservations totales</div>
        </div>
        <div class="stat-card alerte">
            <div class="chiffre"><?= $nb_attente ?></div>
            <div class="label-stat">⏳ En attente</div>
        </div>
        <div class="stat-card success">
            <div class="chiffre"><?= $nb_confirmee ?></div>
            <div class="label-stat">✅ Confirmées</div>
        </div>
    </div>

    <!-- ── Réservations ── -->
    <div class="section-titre">
        📋 Réservations
        <span style="font-size:13px; color:#888; font-weight:normal;">(les plus récentes en premier)</span>
    </div>

    <!-- Filtres par statut -->
    <div class="filtres">
        <button class="filtre-btn actif" onclick="filtrerStatut('tous', this)">Toutes (<?= $nb_resas ?>)</button>
        <button class="filtre-btn" onclick="filtrerStatut('en_attente', this)">⏳ En attente (<?= $nb_attente ?>)</button>
        <button class="filtre-btn" onclick="filtrerStatut('confirmee', this)">✅ Confirmées (<?= $nb_confirmee ?>)</button>
        <button class="filtre-btn" onclick="filtrerStatut('annulee', this)">❌ Annulées</button>
    </div>

    <!-- Barre de recherche -->
    <input type="text" class="search-bar" id="search-resa"
           placeholder="🔍 Rechercher par client, email, véhicule..."
           oninput="rechercherResa()">

    <table id="table-reservations">
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
                <th>Total €</th>
                <th>Assurance</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($reservations)): ?>
            <tr><td colspan="12" class="vide">Aucune réservation pour le moment.</td></tr>
        <?php else: ?>
            <?php foreach ($reservations as $r): ?>
                <?php
                    $d1    = new DateTime($r['date_debut']);
                    $d2    = new DateTime($r['date_fin']);
                    $jours = $d2->diff($d1)->days;
                    $debut_fr = date('d/m/Y', strtotime($r['date_debut']));
                    $fin_fr   = date('d/m/Y', strtotime($r['date_fin']));
                    $nom_voiture = $noms_voitures[$r['voiture']] ?? strtoupper($r['voiture']);
                    $prix_jour   = $tarifs[$r['voiture']] ?? 0;
                    $total_resa  = $prix_jour * $jours;
                    $assurances_lib = ['tous-risques'=>'Tous risques','tiers'=>'Au tiers','vol-incendie'=>'Vol & incendie'];
                    $assurance_label = $assurances_lib[$r['assurance']] ?? $r['assurance'];
                ?>
                <tr data-statut="<?= $r['statut'] ?>">
                    <td><strong>#<?= $r['id'] ?></strong></td>
                    <td><?= htmlspecialchars($nom_voiture) ?></td>
                    <td><?= htmlspecialchars($r['nom']) ?></td>
                    <td style="font-size:12px;"><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['telephone']) ?></td>
                    <td><?= $debut_fr ?></td>
                    <td><?= $fin_fr ?></td>
                    <td><?= $jours ?> j</td>
                    <td><strong><?= $total_resa > 0 ? $total_resa.' €' : '—' ?></strong></td>
                    <td><?= $assurance_label ?></td>
                    <td>
                        <span class="badge-statut <?= $r['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $r['statut'])) ?>
                        </span>
                    </td>
                    <td style="white-space:nowrap;">
                        <!-- Changer le statut directement -->
                        <form method="POST" action="admin.php" style="display:inline-flex; gap:4px; align-items:center;">
                            <input type="hidden" name="action" value="changer_statut">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <select name="statut" class="select-statut">
                                <option value="en_attente" <?= $r['statut']==='en_attente'?'selected':'' ?>>⏳ En attente</option>
                                <option value="confirmee"  <?= $r['statut']==='confirmee' ?'selected':'' ?>>✅ Confirmer</option>
                                <option value="annulee"    <?= $r['statut']==='annulee'   ?'selected':'' ?>>❌ Annuler</option>
                            </select>
                            <button type="submit" class="btn-sauver">OK</button>
                        </form>
                        &nbsp;
                        <a class="btn-supprimer"
                           href="delete_reservation.php?id=<?= (int)$r['id'] ?>"
                           onclick="return confirm('Supprimer la réservation #<?= $r['id'] ?> ?')">
                            🗑
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- ── Utilisateurs ── -->
    <div class="section-titre" style="margin-top:40px;">👥 Utilisateurs inscrits</div>

    <input type="text" class="search-bar" id="search-user"
           placeholder="🔍 Rechercher par nom ou email..."
           oninput="rechercherUser()">

    <table id="table-users">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Inscrit le</th>
                <th>Réservations</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="7" class="vide">Aucun utilisateur.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $u):
                // Compte les réservations de cet utilisateur
                $nb_resa_user = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE email = ?");
                $nb_resa_user->execute([$u['email']]);
                $count_resa = $nb_resa_user->fetchColumn();
            ?>
                <tr>
                    <td><strong>#<?= $u['id'] ?></strong></td>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td style="font-size:12px;"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge-role <?= $u['role'] ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['cree_le'])) ?></td>
                    <td>
                        <span style="background:#f0f0f0; padding:2px 8px; border-radius:12px; font-size:12px;">
                            <?= $count_resa ?> résa
                        </span>
                    </td>
                    <td>
                        <?php if ($u['email'] !== $_SESSION['email']): ?>
                            <a class="btn-supprimer"
                               href="delete_user.php?id=<?= (int)$u['id'] ?>"
                               onclick="return confirm('Supprimer <?= htmlspecialchars($u['nom']) ?> ?')">
                                🗑 Supprimer
                            </a>
                        <?php else: ?>
                            <span style="color:#aaa; font-size:12px;">— vous —</span>
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
        <a href="../pages/faq.php">FAQ</a>
        <a href="../pages/mentions.php">Mentions légales</a>
        <a href="../pages/information.php">Divers information</a>
    </nav>
</footer>

<script>
// ── Filtre par statut ─────────────────────────────────────────
function filtrerStatut(statut, btn) {
    document.querySelectorAll('.filtre-btn').forEach(b => b.classList.remove('actif'));
    btn.classList.add('actif');
    document.querySelectorAll('#table-reservations tbody tr').forEach(tr => {
        tr.style.display = (statut === 'tous' || tr.dataset.statut === statut) ? '' : 'none';
    });
}

// ── Recherche dans les réservations ──────────────────────────
function rechercherResa() {
    const q = document.getElementById('search-resa').value.toLowerCase();
    document.querySelectorAll('#table-reservations tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ── Recherche dans les utilisateurs ──────────────────────────
function rechercherUser() {
    const q = document.getElementById('search-user').value.toLowerCase();
    document.querySelectorAll('#table-users tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

</body>
</html>
