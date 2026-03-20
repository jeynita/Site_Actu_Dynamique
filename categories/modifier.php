<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO(); $id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute([':id' => $id]); $cat = $stmt->fetch();
if (!$cat) { header('Location: liste.php'); exit; }
$erreurs = []; $nom = $cat['nom'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$nom = trim($_POST['nom'] ?? '');
if (empty($nom)) $erreurs[] = 'Le nom est obligatoire.';
if (empty($erreurs)) {
$stmt = $pdo->prepare('UPDATE categories SET nom = :nom WHERE id = :id');
$stmt->execute([':nom' => $nom, ':id' => $id]);
header('Location: liste.php'); exit;
}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Modifier categorie</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Modifier la categorie</h1>
<?php foreach ($erreurs as $e): ?>
<div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form method="post">
<div class="champ">
<label>Nom *</label>
<input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>">
</div>
<button type="submit" class="btn-primaire">Enregistrer</button>
<a href="liste.php" class="btn-secondaire">Annuler</a>
</form>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body></html>
