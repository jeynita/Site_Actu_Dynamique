<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']);
$pdo = getPDO(); $id = (int)($_GET['id'] ?? 0);
if ($id > 0 && $id !== (int)$_SESSION['utilisateur']['id']) {
$stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = :id');
$stmt->execute([':id' => $id]);
}
header('Location: liste.php');
exit;