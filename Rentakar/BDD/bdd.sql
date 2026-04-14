-- ═══════════════════════════════════════════
-- CRÉATION DE LA BASE DE DONNÉES
-- ═══════════════════════════════════════════
CREATE DATABASE IF NOT EXISTS Rentakar
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE Rentakar;

-- ───────────────────────────────────────────
-- TABLE : utilisateurs
-- Stocke les comptes (clients + admins)
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS utilisateurs (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe  VARCHAR(255) NOT NULL,  -- hash bcrypt
  role          ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  cree_le       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ───────────────────────────────────────────
-- TABLE : reservations
-- Stocke toutes les demandes de location
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reservations (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  voiture           VARCHAR(50)  NOT NULL,  -- clé modèle (ex: 'clio')
  nom               VARCHAR(100) NOT NULL,
  email             VARCHAR(150) NOT NULL,
  telephone         VARCHAR(20)  NOT NULL,
  date_debut        DATE NOT NULL,
  date_fin          DATE NOT NULL,
  assurance         ENUM('tous-risques', 'tiers', 'vol-incendie') NOT NULL,
  date_reservation  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  statut            ENUM('en_attente', 'confirmee', 'annulee') NOT NULL DEFAULT 'en_attente'
) ENGINE=InnoDB;

-- ───────────────────────────────────────────
-- COMPTE ADMIN PAR DÉFAUT
-- Mot de passe : admin123 (à changer !)
-- ───────────────────────────────────────────
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES (
  'Admin',
  'admin@rentakar.fr',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hash de 'password'
  'admin'
);