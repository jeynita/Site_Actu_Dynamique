<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']); 

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE id_categorie = :id AND est_supprime = 0");
    $check->execute([':id' => $id]);
    $count = $check->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: liste.php?msg=supprime');
    } else {
        header('Location: liste.php?erreur=pas_vide');
    }
} else {
    header('Location: liste.php');
}
exit;