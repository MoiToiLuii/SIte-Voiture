<?php
// ============================================================
// TRAITEMENT DE LA RÉSERVATION – reservation.php
// Reçoit les données du formulaire de location (POST),
// valide les champs, insère en BDD, puis redirige vers confirmation.
// ============================================================

session_start();
require 'config.php';

// ── 1. Protection par session ────────────────────────────────
// L'utilisateur doit être connecté pour pouvoir réserver
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.html");
    exit;
}

// ── 2. Vérification de la méthode HTTP ──────────────────────
// On n'accepte que les soumissions de formulaire (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/location.php");
    exit;
}

// ── 3. Récupération et nettoyage des données ────────────────
// htmlspecialchars() protège contre les injections XSS
// trim() supprime les espaces inutiles en début/fin
$voiture    = htmlspecialchars(trim($_POST['voiture']));
$nom        = htmlspecialchars(trim($_POST['nom']));
$email      = htmlspecialchars(trim($_POST['email']));
$telephone  = htmlspecialchars(trim($_POST['telephone']));
$date_debut = $_POST['date_debut']; // Format YYYY-MM-DD (natif HTML date)
$date_fin   = $_POST['date_fin'];
$assurance  = htmlspecialchars($_POST['assurance']);

// ── 4. Validations métier ────────────────────────────────────

// Tous les champs doivent être remplis
if (empty($voiture) || empty($nom) || empty($email) || empty($telephone) || empty($date_debut) || empty($date_fin) || empty($assurance)) {
    header("Location: ../pages/location.php?erreur=champs_vides");
    exit;
}

// La date de début ne peut pas être dans le passé
$aujourd_hui = date('Y-m-d');
if ($date_debut < $aujourd_hui) {
    header("Location: ../pages/location.php?erreur=date_passee");
    exit;
}

// La date de fin doit être strictement après la date de début
if ($date_fin <= $date_debut) {
    header("Location: ../pages/location.php?erreur=date_invalide");
    exit;
}

// ── 5. Calcul de la durée en jours ──────────────────────────
$d1       = new DateTime($date_debut);
$d2       = new DateTime($date_fin);
$nb_jours = $d2->diff($d1)->days; // Nombre entier de jours

// ── 6. Insertion en base de données ─────────────────────────
// Requête préparée avec paramètres nommés → protection contre les injections SQL
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

    // Récupère l'ID auto-incrémenté de la réservation qui vient d'être créée
    $id_reservation = $pdo->lastInsertId();

    // ── 7. Stockage en session pour la page de confirmation ──
    // On utilise la session plutôt que l'URL pour éviter d'exposer les données
    $_SESSION['confirmation'] = [
        'id'         => $id_reservation,
        'voiture'    => $voiture,
        'nom'        => $nom,
        'email'      => $email,
        'date_debut' => $date_debut,
        'date_fin'   => $date_fin,
        'nb_jours'   => $nb_jours,
        'assurance'  => $assurance,
    ];

    // Redirection vers la page de récapitulatif
    header("Location: ../pages/confirmation.php");
    exit;

} catch (Exception $e) {
    // En cas d'erreur BDD (ex: champ manquant, contrainte violée)
    // En production : logger l'erreur sans l'afficher à l'utilisateur
    die("Erreur lors de la réservation : " . $e->getMessage());
}
?>
