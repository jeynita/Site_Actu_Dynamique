<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']);

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    
    $stmtImg = $pdo->prepare('SELECT image FROM articles WHERE id = :id');
    $stmtImg->execute([':id' => $id]);
    $img = $stmtImg->fetchColumn();
    
    if ($img && file_exists(__DIR__ . '/../uploads/' . $img)) {
        unlink(__DIR__ . '/../uploads/' . $img);
    }

    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: corbeille.php?msg=detruit');
exit;