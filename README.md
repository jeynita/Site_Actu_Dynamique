## Site_Actu_Dynamique
Site web d'actualité dynamique permettant la consultation publique d'articles et la gestion de contenu par des utilisateurs autorisés

## Membres

- Dieynaba BALDE
- Diarra DIA
- Rokhoya GUEYE

## Installation

1. Cloner le projet
2. Importer le fichier database.sql dans MySQL
3. Configurer la connexion dans config/database.php
4. Lancer le serveur (XAMPP, WAMP, etc.)
5. Accéder au projet via http://localhost/Site_Actu_Dynamique

## Technologies

- PHP (PDO)
- MySQL
- HTML / CSS
- JavaScript

## Fonctionnalités

### Visiteur
- Consulter les articles
- Voir le détail d’un article
- Filtrer par catégorie

### Éditeur
- Ajouter un article
- Modifier un article
- Supprimer un article
- Gérer les catégories

### Administrateur
- Gérer les utilisateurs
- Accès complet à l’application

## Structure du projet

projet/
├── config/
├── includes/
├── auth/
├── articles/
├── categories/
├── utilisateurs/
├── assets/
└── index.php

## Base de données

Le projet utilise une base MySQL avec les tables suivantes :
- utilisateurs
- articles
- categories

Le script SQL est fourni dans le fichier `database.sql`.

## Sécurité

- Utilisation de requêtes préparées (PDO) contre les injections SQL
- Protection des pages via les sessions
- Validation des formulaires côté client (JavaScript) et serveur (PHP)
- Protection contre les failles XSS avec htmlspecialchars()

##Commandes Git essentielles
git init
git add .
git commit -m "Initial commit"
