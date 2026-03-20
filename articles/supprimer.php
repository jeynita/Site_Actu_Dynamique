<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
$stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
$stmt->execute([':id' => $id]);
}
header('Location: liste.php');
exit;