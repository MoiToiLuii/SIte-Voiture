<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    // Vérifier si email existe déjà
    $check = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        echo "Email déjà utilisé";
        exit;
    }

    // Insérer utilisateur
    $sql = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
    $sql->execute([$nom, $email, $mot_de_passe]);

    echo "Inscription réussie ! <a href='../HTML/login.html'>Se connecter</a>";
}
?>