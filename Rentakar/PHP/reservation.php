<?php

session_start();
require "config.php";

// Vérifier la session avant d'accepter la réservation
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "Vous devez être connecté pour réserver.";
    exit;
}

require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        $sql = $pdo->prepare("
            INSERT INTO reservations
            (voiture, nom, email, telephone, date_debut, date_fin, assurance)
            VALUES (:voiture, :nom, :email, :telephone, :date_debut, :date_fin, :assurance)
        ");

        $sql->execute([
            "voiture" => $_POST["voiture"],
            "nom" => $_POST["nom"],
            "email" => $_POST["email"],
            "telephone" => $_POST["telephone"],
            "date_debut" => $_POST["date_debut"],
            "date_fin" => $_POST["date_fin"],
            "assurance" => $_POST["assurance"]
        ]);

        echo "✅ Réservation enregistrée avec succès !";

    } catch (Exception $e) {
        echo "❌ Erreur : " . $e->getMessage();
    }
}
?>