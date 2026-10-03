# Site Actu Dynamique

> Site web d'actualité dynamique : consultation publique d'articles et gestion de contenu par des utilisateurs autorisés.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)

## Fonctionnalités

Le site gère trois profils avec des droits différents :

| Profil | Droits |
|---|---|
| **Visiteur** | Consulter les articles, voir le détail d'un article, filtrer par catégorie |
| **Éditeur** | Ajouter, modifier et supprimer des articles, gérer les catégories |
| **Administrateur** | Gérer les utilisateurs, accès complet à l'application |

## Technologies

- **Back-end** : PHP (PDO)
- **Base de données** : MySQL
- **Front-end** : HTML, CSS, JavaScript

## Structure du projet

```
Site_Actu_Dynamique/
├── config/          # Connexion à la base de données
├── includes/        # Éléments communs (header, footer...)
├── auth/            # Connexion, déconnexion, gestion des sessions
├── articles/        # CRUD des articles
├── categories/      # Gestion des catégories
├── utilisateurs/    # Gestion des utilisateurs (admin)
├── assets/          # CSS, JavaScript, images
├── database.sql     # Script de création de la base
└── index.php        # Page d'accueil
```

## Base de données

Trois tables : `utilisateurs`, `articles`, `categories`.
Le script de création est fourni dans `database.sql`.

## Installation

1. Cloner le projet dans le dossier du serveur local (`htdocs` pour XAMPP, `www` pour WAMP) :
```bash
   git clone https://github.com/jeynita/Site_Actu_Dynamique.git
```
2. Créer une base de données MySQL puis importer `database.sql` (phpMyAdmin → *Importer*).
3. Configurer la connexion dans `config/database.php` (hôte, nom de la base, utilisateur, mot de passe).
4. Démarrer Apache et MySQL (XAMPP, WAMP...).
5. Ouvrir http://localhost/Site_Actu_Dynamique

## Sécurité

- Requêtes préparées (PDO) contre les injections SQL
- Pages protégées par sessions, selon le profil
- Validation des formulaires côté client (JavaScript) et côté serveur (PHP)
- Protection contre les failles XSS avec `htmlspecialchars()`


- Pagination des articles
- Barre de recherche
- Upload d'images pour les articles
