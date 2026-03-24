<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Sécurité : Seuls les éditeurs et admins peuvent archiver
autoriser(['editeur', 'administrateur']);

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
       
        $stmt = $pdo->prepare('UPDATE articles SET est_supprime = 1 WHERE id = :id');
        $stmt->execute([':id' => $id]);
        
        // Optionnel : on peut ajouter un message de succès dans l'URL
        header('Location: liste.php?status=archived');
    } catch (PDOException $e) {
        // En cas d'erreur SQL
        header('Location: liste.php?status=error');
    }
} else {
    header('Location: liste.php');
}
exit;