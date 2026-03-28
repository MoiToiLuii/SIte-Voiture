<?php
// Configuration de la base de données
$host = "127.0.0.1";  // Serveur XAMPP
$db   = "mon_projet"; // Nom BDD
$user = "root";       
$pass = "";           

include 'config.php';

try {
    $pdo->query("SELECT 1"); // test simple
    echo "Connexion OK à la base de données !";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
