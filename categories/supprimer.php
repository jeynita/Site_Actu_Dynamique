<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']); // Sécurité max : seul l'admin supprime une catégorie

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // 1. Vérifier s'il reste des articles liés
    $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE id_categorie = :id AND est_supprime = 0");
    $check->execute([':id' => $id]);
    $count = $check->fetchColumn();

    if ($count == 0) {
        // 2. Si vide, on supprime vraiment (Hard Delete ici car c'est une structure)
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header('Location: liste.php?msg=supprime');
    } else {
        // 3. Sinon, on renvoie une erreur
        header('Location: liste.php?erreur=pas_vide');
    }
} else {
    header('Location: liste.php');
}
exit;