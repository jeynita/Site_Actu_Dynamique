<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function autoriser(array $roles) 
{
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: /Site_Actu_Dynamique/connexion.php');
        exit;
    }

    if (!in_array($_SESSION['utilisateur']['role'], $roles, true)) {
        http_response_code(403);
        die("<h1>Accès refusé</h1><p>Désolé, votre rôle ne permet pas d'accéder à cette page.</p><a href='/Site_Actu_Dynamique/accueil.php'>Retour</a>");
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