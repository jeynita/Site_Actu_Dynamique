<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('UPDATE articles SET est_supprime = 0 WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: corbeille.php?msg=restaurer');
exit;