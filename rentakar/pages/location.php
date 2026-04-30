<?php
session_start();
require '../php/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

$nom_utilisateur = htmlspecialchars($_SESSION['user']);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Chargement des disponibilités depuis la BDD
$stmt = $pdo->query("
    SELECT voiture, date_debut, date_fin
    FROM reservations
    WHERE statut != 'annulee'
    ORDER BY voiture, date_debut
");
$dispo = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $dispo[$r['voiture']][] = [$r['date_debut'], $r['date_fin']];
}
$dispo_json = json_encode($dispo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RentaKar – Locations</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        header { background-image: url("../images/Logo.png"); }

        .carte.indisponible { opacity: 0.65; }

        .badge-dispo {
            display: inline-block;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            margin-top: 5px;
        }
        .badge-dispo.libre  { background:#d4edda; color:#155724; }
        .badge-dispo.occupe { background:#f8d7da; color:#721c24; }

        .tarif-jour-carte {
            font-size: 13px;
            color: #4A7FA7;
            font-weight: bold;
            margin: 5px 0;
        }

        /* Popup location plus large */
        #popup-location .modern-popup {
            width: 500px;
            max-width: 95vw;
            max-height: 92vh;
            overflow-y: auto;
        }

        .tarif-entete {
            font-size: 16px;
            font-weight: bold;
            color: #0A1931;
            margin-bottom: 14px;
            padding: 8px 12px;
            background: #f0f7ff;
            border-radius: 6px;
            border-left: 3px solid #4A7FA7;
        }

        .recap-tarif {
            background: #f0f7ff;
            border: 1px solid #4A7FA7;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            color: #1A3D63;
            margin: 8px 0;
        }
        .recap-tarif .ligne {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }
        .recap-tarif .ligne-total {
            display: flex;
            justify-content: space-between;
            border-top: 2px solid #4A7FA7;
            margin-top: 8px;
            padding-top: 8px;
            font-size: 16px;
            font-weight: bold;
        }
        .recap-tarif .montant-total { color: #2ecc71; font-size: 18px; }

        /* Jours désactivés dans flatpickr */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            background: #ffe0e0 !important;
            color: #c0392b !important;
            text-decoration: line-through;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

<header>
    <h1>RentaKar</h1>
    <p class="slogan-location">Trouver une voiture qui correspond à votre style</p>
</header>

<nav>
    <a href="../index.php">Accueil</a>
    <a href="location.php">Locations</a>
    <a href="mes_locations.php">Mes locations</a>
    <span style="color:#4A7FA7; font-weight:bold;">👤 <?= $nom_utilisateur ?></span>
    <a href="../php/logout.php">Déconnexion</a>
</nav>

<main>

    <!-- Popup descriptif -->
    <div id="popup-descriptif" class="popup">
        <div class="popup-content modern-popup">
            <span class="close" onclick="fermerPopup()">&times;</span>
            <h2 id="titre-descriptif"></h2>
            <p id="texte-descriptif"></p>
        </div>
    </div>

    <!-- Grille voitures -->
    <section class="grid-voitures">
    <?php
    $voitures = [
        ['clio',    'Renault Clio',      'clio.jpg'],
        ['208',     'Peugeot 208',       '208.jpg'],
        ['i20',     'Hyundai i20',       'Hyundai-i20.png'],
        ['yaris',   'Toyota Yaris GR',   'Yaris.jpg'],
        ['polo',    'Volkswagen Polo',   'vwpolo.png'],
        ['focus',   'Ford Focus',        'ford-focus.jpg'],
        ['classec', 'Mercedes Classe C', 'classe-c.jpg'],
        ['classee', 'Mercedes Classe E', 'classe-e.jpg'],
        ['serie3',  'BMW Série 3',       'bmw-serie3.jpg'],
        ['a4',      'Audi A4',           'audi-a4.jpg'],
        ['x1',      'BMW X1',            'bmw-x1.jpg'],
        ['q5',      'Audi Q5',           'audi-q5.jpg'],
        ['p911',    'Porsche 911',       'porsche-911.jpg'],
        ['r8',      'Audi R8',           'audi-r8.jpg'],
        ['amggt',   'Mercedes AMG GT',  'mercedes-amg-gt.jpg'],
    ];
    $tarifs_php = [
        'clio'=>45,'208'=>50,'i20'=>48,'yaris'=>47,'polo'=>52,
        'focus'=>55,'classec'=>90,'classee'=>110,'serie3'=>95,
        'a4'=>100,'x1'=>120,'q5'=>130,'p911'=>350,'r8'=>500,'amggt'=>600
    ];

    foreach ($voitures as [$cle, $nom, $img]):
        $occupe = false;
        if (isset($dispo[$cle])) {
            $today = date('Y-m-d');
            foreach ($dispo[$cle] as $p) {
                if ($today >= $p[0] && $today <= $p[1]) { $occupe = true; break; }
            }
        }
    ?>
        <div class="carte <?= $occupe ? 'indisponible' : '' ?>">
            <img src="../images/<?= $img ?>" alt="<?= $nom ?>">
            <h3><?= $nom ?></h3>
            <div class="tarif-jour-carte"><?= $tarifs_php[$cle] ?> € / jour</div>
            <span class="badge-dispo <?= $occupe ? 'occupe' : 'libre' ?>">
                <?= $occupe ? '🔴 Louée aujourd\'hui' : '🟢 Disponible' ?>
            </span><br>
            <button onclick="ouvrirDescriptif('<?= $cle ?>')">Descriptif</button>
            <button class="bouton-louer" onclick="ouvrirLocation('<?= $cle ?>')">Louer</button>
        </div>
    <?php endforeach; ?>
    </section>

</main>

<!-- Popup location -->
<div id="popup-location" class="popup">
    <div class="popup-content modern-popup">
        <span class="close" onclick="document.getElementById('popup-location').style.display='none'">&times;</span>
        <h2>Location : <span id="modele-location"></span></h2>

        <div id="tarif-entete" class="tarif-entete"></div>

        <form method="POST" action="../php/reservation.php" class="form-modern" id="form-reservation">
            <input type="hidden" name="voiture" id="voiture">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

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
                <input type="text" name="date_debut" id="date_debut"
                       placeholder="Sélectionner une date" readonly required>
            </div>
            <div class="form-group">
                <label>Date de fin :</label>
                <input type="text" name="date_fin" id="date_fin"
                       placeholder="Sélectionner une date" readonly required>
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

            <!-- Récap tarif — affiché dès que les deux dates sont choisies -->
            <div id="recap-tarif" class="recap-tarif" style="display:none;">
                <div class="ligne">
                    <span>Durée :</span>
                    <strong><span id="nb-jours-affiche">0</span> jour(s)</strong>
                </div>
                <div class="ligne">
                    <span>Tarif / jour :</span>
                    <strong><span id="prix-jour-affiche"></span></strong>
                </div>
                <div class="ligne-total">
                    <span>Total estimé :</span>
                    <span class="montant-total" id="total-location"></span>
                </div>
            </div>

            <button type="submit" class="btn-green" style="margin-top:10px;">
                ✅ Confirmer la location
            </button>
        </form>
    </div>
</div>

<footer>
    <nav>
        <a href="faq.php">FAQ</a>
        <a href="mentions.php">Mentions légales</a>
        <a href="information.php">Divers information</a>
    </nav>
</footer>

<!-- ============================================================
     TOUT LE JAVASCRIPT EN UN SEUL BLOC
     On n'importe PAS script.js pour éviter les conflits de fonctions.
     Tout ce dont on a besoin est redéfini ici.
============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

<script>
// ── Données injectées par PHP ─────────────────────────────────
const disponibilites = <?= $dispo_json ?>;

// ── Descriptions des voitures ─────────────────────────────────
const descriptions = {
    clio:    "Renault Clio – Citadine polyvalente, idéale pour la ville. Consommation faible, excellente maniabilité. Puissance : environ 90 ch.",
    "208":   "Peugeot 208 – Moderne, confortable et dynamique. Parfaite pour un usage urbain. Puissance : environ 100 ch.",
    i20:     "Hyundai i20 – Fiable, économique et bien équipée. Excellent rapport qualité/prix. Puissance : environ 84 ch.",
    yaris:   "Toyota Yaris GR – Version sportive dérivée du rallye. Moteur 1.6 turbo 261 ch, transmission intégrale.",
    polo:    "Volkswagen Polo – Finition haut de gamme, confort allemand. Très stable sur autoroute. Puissance : environ 95 ch.",
    focus:   "Ford Focus – Compacte polyvalente, idéale ville et longs trajets. Moteur réactif. Puissance : environ 120 ch.",
    classec: "Mercedes-Benz Classe C – Berline premium, confort, élégance et technologies modernes. Puissance : environ 170 ch.",
    classee: "Mercedes-Benz Classe E – Grande berline luxueuse pour longs trajets. Confort exceptionnel. Puissance : environ 200 ch.",
    serie3:  "BMW Série 3 – Berline sportive, dynamisme et plaisir de conduite. Direction précise. Puissance : environ 184 ch.",
    a4:      "Audi A4 – Berline premium élégante et technologique. Finition impeccable. Puissance : environ 150 ch.",
    x1:      "BMW X1 – SUV compact dynamique et polyvalent. Position de conduite haute. Puissance : environ 150 ch.",
    q5:      "Audi Q5 – SUV premium confortable et raffiné. Technologies avancées. Puissance : environ 190 ch.",
    p911:    "Porsche 911 – Icône sportive. Moteur flat-six, comportement ultra précis. Puissance : environ 385 ch.",
    r8:      "Audi R8 – Supercar V10 atmosphérique. Sonorité incroyable, performances explosives. Puissance : environ 570 ch.",
    amggt:   "Mercedes-AMG GT – Coupé sportif hautes performances. Moteur V8 biturbo. Puissance : environ 530 ch."
};

// ── Tarifs par modèle ─────────────────────────────────────────
const tarifs = {
    clio:45, "208":50, i20:48, yaris:47, polo:52,
    focus:55, classec:90, classee:110, serie3:95,
    a4:100, x1:120, q5:130, p911:350, r8:500, amggt:600
};

// ── Instances Flatpickr ───────────────────────────────────────
let fpDebut = null;
let fpFin   = null;
let modeleActuel = null;

// ── Popup DESCRIPTIF ─────────────────────────────────────────
function ouvrirDescriptif(modele) {
    if (!descriptions[modele]) return;
    const texte = descriptions[modele];
    document.getElementById('titre-descriptif').textContent = texte.split('–')[0].trim();
    document.getElementById('texte-descriptif').textContent = texte;
    document.getElementById('popup-descriptif').style.display = 'flex';
}
function fermerPopup() {
    document.getElementById('popup-descriptif').style.display = 'none';
}

// ── Popup LOCATION ────────────────────────────────────────────
function ouvrirLocation(modele) {
    if (!descriptions[modele]) return;

    modeleActuel = modele;

    // Remplissage des infos
    document.getElementById('modele-location').textContent =
        descriptions[modele].split('–')[0].trim();
    document.getElementById('voiture').value = modele;
    document.getElementById('tarif-entete').textContent =
        '💰 Tarif : ' + tarifs[modele] + ' € / jour';

    // Reset formulaire et récap
    document.getElementById('form-reservation').reset();
    document.getElementById('voiture').value = modele; // reset() l'efface, on remet
    document.getElementById('recap-tarif').style.display = 'none';

    // Affiche le popup
    document.getElementById('popup-location').style.display = 'flex';

    // Init datepickers après que le popup soit visible
    setTimeout(() => initDatepickers(modele), 80);
}

// ── Initialisation Flatpickr ──────────────────────────────────
function initDatepickers(modele) {
    // Dates désactivées pour ce modèle : tableau d'objets {from, to}
    const disabled = (disponibilites[modele] || []).map(p => ({ from: p[0], to: p[1] }));
    const today    = new Date().toISOString().split('T')[0];

    if (fpDebut) fpDebut.destroy();
    if (fpFin)   fpFin.destroy();

    fpDebut = flatpickr('#date_debut', {
        locale:     'fr',
        dateFormat: 'Y-m-d',
        minDate:    today,
        disable:    disabled,
        onChange(selectedDates) {
            if (!selectedDates.length) return;
            // Le lendemain de la date de début devient le min de date_fin
            const next = new Date(selectedDates[0]);
            next.setDate(next.getDate() + 1);
            fpFin.set('minDate', next);
            calculerRecap();
        }
    });

    fpFin = flatpickr('#date_fin', {
        locale:     'fr',
        dateFormat: 'Y-m-d',
        minDate:    today,
        disable:    disabled,
        onChange()  { calculerRecap(); }
    });
}

// ── Calcul du récapitulatif tarifaire ────────────────────────
function calculerRecap() {
    const debut   = document.getElementById('date_debut').value;
    const fin     = document.getElementById('date_fin').value;
    const recapEl = document.getElementById('recap-tarif');

    if (!debut || !fin || !modeleActuel) { recapEl.style.display = 'none'; return; }

    const jours = Math.ceil((new Date(fin) - new Date(debut)) / 86400000);
    if (jours <= 0) { recapEl.style.display = 'none'; return; }

    const prixJour = tarifs[modeleActuel] || 0;
    const total    = prixJour * jours;

    document.getElementById('nb-jours-affiche').textContent  = jours;
    document.getElementById('prix-jour-affiche').textContent = prixJour + ' €';
    document.getElementById('total-location').textContent    = total + ' €';
    recapEl.style.display = 'block';
}

// ── Avis clients dynamiques (ex script.js) ───────────────────
const avis = [
    { nom: "Karim B.",  commentaire: "Service rapide et très professionnel. Large choix de véhicules.", note: 5 },
    { nom: "Sarah M.",  commentaire: "Très bonne expérience, site simple et efficace.", note: 5 },
    { nom: "Thomas L.", commentaire: "Location facile et rapide, je recommande vivement.", note: 5 },
    { nom: "Julie R.",  commentaire: "Voiture impeccable, service client au top.", note: 4 },
    { nom: "Mehdi A.",  commentaire: "Prix corrects et réservation ultra simple.", note: 5 },
    { nom: "Laura P.",  commentaire: "J'ai trouvé exactement la voiture qu'il me fallait.", note: 4 }
];
const zone = document.getElementById("avis-dynamique");
if (zone) {
    let idx = 0;
    function afficherAvis() {
        const c = avis[idx];
        zone.innerHTML = `<div class="carte-avis animate">
            <div class="etoiles">${"★".repeat(c.note)}${"☆".repeat(5-c.note)}</div>
            <p><strong>${c.nom}</strong></p>
            <p>${c.commentaire}</p>
        </div>`;
        idx = (idx + 1) % avis.length;
    }
    afficherAvis();
    setInterval(afficherAvis, 4000);
}
</script>

</body>
</html>
