<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

autoriser(['editeur', 'administrateur']);

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare('UPDATE articles SET est_supprime = 1 WHERE id = :id');
        $stmt->execute([':id' => $id]);
        
        header('Location: liste.php?status=archived');
    } catch (PDOException $e) {
        header('Location: liste.php?status=error');
    }
} else {
    header('Location: liste.php');
}
exit;