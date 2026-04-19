-- ═══════════════════════════════════════════════════════════════
-- BASE DE DONNÉES RENTAKAR
-- À importer dans phpMyAdmin (XAMPP) ou via : mysql -u root < bdd.sql
-- ═══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS Rentakar
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE Rentakar;

-- ───────────────────────────────────────────────────────────────
-- TABLE : utilisateurs
-- Stocke les comptes clients ET administrateurs.
-- Le champ mot_de_passe contient toujours un hash bcrypt (PHP password_hash).
-- ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS utilisateurs (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe  VARCHAR(255) NOT NULL,                         -- hash bcrypt, jamais en clair
  role          ENUM('user', 'admin') NOT NULL DEFAULT 'user', -- 'user' par défaut à l'inscription
  cree_le       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ───────────────────────────────────────────────────────────────
-- TABLE : reservations
-- Stocke toutes les demandes de location soumises via le formulaire.
-- Le statut passe de 'en_attente' à 'confirmee' ou 'annulee' via le panel admin.
-- ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reservations (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  voiture           VARCHAR(50)  NOT NULL,  -- clé modèle (ex: 'clio', 'p911', 'r8')
  nom               VARCHAR(100) NOT NULL,
  email             VARCHAR(150) NOT NULL,
  telephone         VARCHAR(20)  NOT NULL,
  date_debut        DATE NOT NULL,
  date_fin          DATE NOT NULL,
  assurance         ENUM('tous-risques', 'tiers', 'vol-incendie') NOT NULL,
  date_reservation  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut            ENUM('en_attente', 'confirmee', 'annulee') NOT NULL DEFAULT 'en_attente'
) ENGINE=InnoDB;

-- ───────────────────────────────────────────────────────────────
-- COMPTE ADMINISTRATEUR PAR DÉFAUT
--
-- ✅ Email    : admin@rentakar.fr
-- ✅ Mot de passe : admin123
--
-- Ce hash bcrypt a été généré avec : password_hash('admin123', PASSWORD_DEFAULT)
-- Vous pouvez le changer en vous connectant et en modifiant via phpMyAdmin,
-- ou en regénérant un hash avec : echo password_hash('votre_mdp', PASSWORD_DEFAULT);
-- ───────────────────────────────────────────────────────────────
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES (
  'Admin',
  'admin@rentakar.fr',
  '$2y$10$TKh8H1.PfYkoOFnPECO7dOm07brkfnzKCidmh4Fp3sNnFHhOSHjCi', -- hash de 'admin123'
  'admin'
);
