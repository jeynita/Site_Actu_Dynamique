-- ============================================================
--  Site d'actualité dynamique — Script SQL complet
-- ============================================================

CREATE DATABASE IF NOT EXISTS site_actualite
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE site_actualite;

-- ------------------------------------------------------------
-- Table : categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id  INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : utilisateurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateurs (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL,
    prenom       VARCHAR(100) NOT NULL,
    login        VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('editeur','administrateur') NOT NULL DEFAULT 'editeur',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : articles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS articles (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    titre              VARCHAR(255) NOT NULL,
    description_courte TEXT NOT NULL,
    contenu            LONGTEXT NOT NULL,
    date_publication   DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_categorie       INT NOT NULL,
    id_auteur          INT NOT NULL,
    CONSTRAINT fk_categorie FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_auteur    FOREIGN KEY (id_auteur)    REFERENCES utilisateurs(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Données initiales
-- ------------------------------------------------------------

INSERT INTO categories (nom) VALUES
    ('Technologie'),
    ('Sport'),
    ('Politique'),
    ('Éducation'),
    ('Culture');

-- Mot de passe : password (bcrypt)
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
    ('Diallo', 'Amadou', 'admin',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur'),
    ('Ndiaye', 'Fatou',  'editeur', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editeur');

INSERT INTO articles (titre, description_courte, contenu, id_categorie, id_auteur) VALUES
    (
        'Intelligence artificielle : les avancées de 2026',
        'Un tour d\'horizon des principales évolutions de l\'IA cette année.',
        'L\'intelligence artificielle continue de transformer de nombreux secteurs en 2026. Les modèles de langage sont devenus omniprésents dans les entreprises, les hôpitaux et les établissements d\'enseignement. Cet article analyse les tendances majeures et leurs impacts sur la société sénégalaise.',
        1, 2
    ),
    (
        'CAN 2026 : le Sénégal en demi-finale',
        'Les Lions de la Téranga ont éliminé le Maroc aux tirs au but.',
        'Dans un match haletant disputé hier soir à Dakar, l\'équipe nationale sénégalaise a validé son billet pour le dernier carré de la compétition continentale. Le gardien a été décisif lors de la séance de tirs au but, arrêtant deux tentatives adverses.',
        2, 2
    ),
    (
        'Réforme de l\'enseignement supérieur au Sénégal',
        'Le gouvernement annonce un plan de modernisation des universités publiques.',
        'Le ministère de l\'Enseignement supérieur a présenté un vaste programme de réforme visant à améliorer la qualité de la formation et à renforcer l\'insertion professionnelle des diplômés. Parmi les mesures phares figurent la digitalisation des cours et le renforcement des partenariats avec le secteur privé.',
        4, 2
    );