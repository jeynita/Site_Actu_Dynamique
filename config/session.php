<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function autoriser(array $roles): void
{
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: /Site_Actu_Dynamique/connexion.php');
        exit;
    }
    if (!in_array($_SESSION['utilisateur']['role'], $roles, true)) {
        http_response_code(403);
        die('Accès refusé. Vous n\'avez pas les droits nécessaires.');
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