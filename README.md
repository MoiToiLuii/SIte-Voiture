# RentaKar

Projet web de location de voitures réalisé en PHP, MySQL, HTML, CSS et JavaScript [1][2][3].

## Installation

1. Placer le dossier `rentakar` dans le dossier `htdocs` de XAMPP [1].
2. Lancer **Apache** et **MySQL** depuis XAMPP [1].
3. Si MySQL ne démarre pas, aller dans le gestionnaire de tâches et arrêter le processus `sql` si nécessaire [1].
4. Ouvrir dans le navigateur : `http://localhost/rentakar/gen_hash.php` [4].
5. Copier le hash généré et le remplacer dans le fichier SQL à l’endroit indiqué pour le mot de passe administrateur `admin123` [1][4].
6. Importer ensuite le contenu du fichier `bdd.sql` dans phpMyAdmin via : `http://localhost/phpmyadmin/index.php?route=/server/sql` [1].
7. Ouvrir enfin le site avec : `http://localhost/rentakar/index.php` [2].

## Fonctionnalités

- Consultation des véhicules [2][5].
- Affichage du descriptif d’un véhicule [2][5].
- Réservation via formulaire [1][5].
- Gestion des utilisateurs et des réservations [1][5].
- Interface administrateur [5].

## Base de données

Le projet utilise une base de données `Rentakar` contenant principalement deux tables :

- `utilisateurs` : comptes clients et administrateurs [1].
- `reservations` : demandes de location enregistrées [1].

## Remarque

Ne pas saisir uniquement `0` dans le numéro de téléphone lors d’une réservation.

