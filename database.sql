

CREATE DATABASE IF NOT EXISTS site_actualite
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE site_actualite;


CREATE TABLE IF NOT EXISTS categories (
    id  INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS utilisateurs (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL,
    prenom       VARCHAR(100) NOT NULL,
    login        VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('editeur','administrateur') NOT NULL DEFAULT 'editeur',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS articles (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    titre              VARCHAR(255) NOT NULL,
    description_courte TEXT NOT NULL,
    contenu            LONGTEXT NOT NULL,
    image              VARCHAR(255) DEFAULT NULL, 
    date_publication   DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_categorie       INT NOT NULL,
    id_auteur          INT NOT NULL,
    CONSTRAINT fk_categorie FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_auteur    FOREIGN KEY (id_auteur)    REFERENCES utilisateurs(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

