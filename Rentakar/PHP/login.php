<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $sql->execute([$email]);

    $user = $sql->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {

        $_SESSION['user'] = $user['nom'];

        header("Location: ../index.html");
        exit;

    } else {
        echo "Email ou mot de passe incorrect";
    }
}
?>