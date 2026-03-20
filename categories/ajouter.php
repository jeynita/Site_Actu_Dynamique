<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO(); $erreurs = []; $nom = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$nom = trim($_POST['nom'] ?? '');
if (empty($nom)) $erreurs[] = 'Le nom est obligatoire.';
if (empty($erreurs)) {
$stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (:nom)');
$stmt->execute([':nom' => $nom]);
header('Location: liste.php'); exit;
}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajouter categorie</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Ajouter une categorie</h1>
<?php foreach ($erreurs as $e): ?>
<div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form id="fCat" method="post" novalidate>
<div class="champ">
<label for="nom">Nom *</label>
<input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>">
<span class="erreur-js" id="err-nom"></span>
</div>
<button type="submit" class="btn-primaire">Ajouter</button>
<a href="liste.php" class="btn-secondaire">Annuler</a>
</form>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
<script>
document.getElementById('fCat').addEventListener('submit', function(e) {
document.getElementById('err-nom').textContent = '';
if (!document.getElementById('nom').value.trim()) {
document.getElementById('err-nom').textContent = 'Le nom est obligatoire.';
e.preventDefault();
}
});
</script>
</body></html>