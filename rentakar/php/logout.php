<?php
// ============================================================
// DÉCONNEXION – logout.php
// Détruit la session active et redirige vers la page d'accueil.
// ============================================================

session_start();   // Reprend la session existante pour pouvoir la détruire
session_destroy(); // Supprime toutes les variables de session ($_SESSION)

// ── Redirection vers l'accueil ───────────────────────────────
// On redirige vers index.php (et non login.html) pour que
// l'utilisateur voit la page d'accueil normale, pas forcément le login.
header("Location: ../index.php");
exit; // Toujours mettre exit après un header redirect
?>
