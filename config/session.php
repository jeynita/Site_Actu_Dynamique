\<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur a le bon rôle pour accéder à la page
 */
function autoriser(array $roles): void
{
    // 1. Si l'utilisateur n'est pas connecté
    if (!isset($_SESSION['utilisateur'])) {
        // On utilise un chemin relatif pour être plus flexible
        // Cela suppose que connexion.php est à la racine du projet
        header('Location: /Site_Actu_Dynamique/connexion.php');
        exit;
    }

    // 2. Si l'utilisateur est connecté mais n'a pas le bon rôle
    if (!in_array($_SESSION['utilisateur']['role'], $roles, true)) {
        http_response_code(403);
        // On affiche un message propre pour ton jury
        die("<h1>Accès refusé</h1><p>Désolé, votre rôle (<b>" . $_SESSION['utilisateur']['role'] . "</b>) ne vous permet pas d'accéder à cette page.</p><a href='/Site_Actu_Dynamique/accueil.php'>Retour à l'accueil</a>");
    }
}

function estConnecte(): bool
{
    return isset($_SESSION['utilisateur']);
}

function getRoleUtilisateur(): ?string
{
    return $_SESSION['utilisateur']['role'] ?? null;
}