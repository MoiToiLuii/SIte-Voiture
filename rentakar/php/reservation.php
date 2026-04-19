<?php
// ============================================================
// TRAITEMENT DE LA RÉSERVATION – reservation.php
// ============================================================

session_start();
require 'config.php';

// ── 1. Protection par session ────────────────────────────────
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.html");
    exit;
}

// ── 2. Méthode POST uniquement ───────────────────────────────
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/location.php");
    exit;
}

// ── 3. Vérification du token CSRF ────────────────────────────
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("❌ Erreur de sécurité : token CSRF invalide. <a href='../pages/location.php'>Retour</a>");
}

// ── 4. Récupération et nettoyage des données ─────────────────
$voiture    = htmlspecialchars(trim($_POST['voiture']));
$nom        = htmlspecialchars(trim($_POST['nom']));
$email      = htmlspecialchars(trim($_POST['email']));
$telephone  = htmlspecialchars(trim($_POST['telephone']));
$date_debut = $_POST['date_debut'];
$date_fin   = $_POST['date_fin'];
$assurance  = htmlspecialchars($_POST['assurance']);

// ── 5. Validations ───────────────────────────────────────────
if (empty($voiture) || empty($nom) || empty($email) || empty($telephone) || empty($date_debut) || empty($date_fin) || empty($assurance)) {
    header("Location: ../pages/location.php?erreur=champs_vides");
    exit;
}

$aujourd_hui = date('Y-m-d');
if ($date_debut < $aujourd_hui) {
    header("Location: ../pages/location.php?erreur=date_passee");
    exit;
}

if ($date_fin <= $date_debut) {
    header("Location: ../pages/location.php?erreur=date_invalide");
    exit;
}

// ── 6. Vérification de disponibilité ─────────────────────────
// On vérifie qu'il n'y a pas de réservation active (non annulée) qui chevauche
$check_dispo = $pdo->prepare("
    SELECT COUNT(*) FROM reservations
    WHERE voiture = :voiture
      AND statut != 'annulee'
      AND date_debut < :date_fin
      AND date_fin   > :date_debut
");
$check_dispo->execute([
    'voiture'    => $voiture,
    'date_debut' => $date_debut,
    'date_fin'   => $date_fin,
]);

if ($check_dispo->fetchColumn() > 0) {
    header("Location: ../pages/location.php?erreur=voiture_indisponible");
    exit;
}

// ── 7. Calcul de la durée ────────────────────────────────────
$d1       = new DateTime($date_debut);
$d2       = new DateTime($date_fin);
$nb_jours = $d2->diff($d1)->days;

// ── 8. Calcul du prix total ──────────────────────────────────
$tarifs = [
    'clio'=>45,'208'=>50,'i20'=>48,'yaris'=>47,'polo'=>52,'focus'=>55,
    'classec'=>90,'classee'=>110,'serie3'=>95,'a4'=>100,'x1'=>120,
    'q5'=>130,'p911'=>350,'r8'=>500,'amggt'=>600
];
$prix_total = ($tarifs[$voiture] ?? 0) * $nb_jours;

// ── 9. Insertion en BDD ──────────────────────────────────────
try {
    $sql = $pdo->prepare("
        INSERT INTO reservations
            (voiture, nom, email, telephone, date_debut, date_fin, assurance)
        VALUES
            (:voiture, :nom, :email, :telephone, :date_debut, :date_fin, :assurance)
    ");

    $sql->execute([
        'voiture'    => $voiture,
        'nom'        => $nom,
        'email'      => $email,
        'telephone'  => $telephone,
        'date_debut' => $date_debut,
        'date_fin'   => $date_fin,
        'assurance'  => $assurance,
    ]);

    $id_reservation = $pdo->lastInsertId();

    // Renouvelle le token CSRF après une soumission réussie
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // ── 10. Stockage en session pour la confirmation ──────────
    $_SESSION['confirmation'] = [
        'id'          => $id_reservation,
        'voiture'     => $voiture,
        'nom'         => $nom,
        'email'       => $email,
        'date_debut'  => $date_debut,
        'date_fin'    => $date_fin,
        'nb_jours'    => $nb_jours,
        'assurance'   => $assurance,
        'prix_total'  => $prix_total,
    ];

    header("Location: ../pages/confirmation.php");
    exit;

} catch (Exception $e) {
    die("Erreur lors de la réservation : " . $e->getMessage());
}
?>
