<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();
if (!$article) { header('Location: liste.php'); exit; }
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$titre = trim($_POST['titre'] ?? '');
$description = trim($_POST['description_courte'] ?? '');
$contenu = trim($_POST['contenu'] ?? '');
$id_cat = (int)($_POST['id_categorie'] ?? 0);
if (empty($titre)) $erreurs[] = 'Le titre est obligatoire.';
if (empty($description)) $erreurs[] = 'La description courte est obligatoire.';
if (empty($contenu)) $erreurs[] = 'Le contenu est obligatoire.';
if ($id_cat <= 0) $erreurs[] = 'Veuillez choisir une categorie.';
if (empty($erreurs)) {
$stmt = $pdo->prepare(
'UPDATE articles SET titre=:titre, description_courte=:desc,
contenu=:contenu, id_categorie=:cat WHERE id=:id'
);
$stmt->execute([':titre'=>$titre,':desc'=>$description,
':contenu'=>$contenu,':cat'=>$id_cat,':id'=>$id]);
header('Location: liste.php'); exit;
}
} else {
$titre = $article['titre']; $description = $article['description_courte'];
$contenu = $article['contenu']; $id_cat = $article['id_categorie'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Modifier article</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Modifier l'article</h1>
<?php foreach ($erreurs as $e): ?>
<div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form method="post" novalidate>
<div class="champ"><label>Titre *</label>
<input type="text" name="titre" value="<?= htmlspecialchars($titre) ?>"></div>
<div class="champ"><label>Description courte *</label>
<textarea name="description_courte" rows="3"><?= htmlspecialchars($description)
?></textarea></div>
<div class="champ"><label>Contenu *</label>
<textarea name="contenu" rows="10"><?= htmlspecialchars($contenu) ?></textarea></div>
<div class="champ"><label>Categorie *</label>
<select name="id_categorie">
<option value="">-- Choisir --</option>
<?php foreach ($categories as $cat): ?>
<option value="<?= $cat['id'] ?>" <?= $id_cat==$cat['id']?'selected':'' ?>>
<?= htmlspecialchars($cat['nom']) ?>
</option>
<?php endforeach; ?>
</select></div>
<button type="submit" class="btn-primaire">Enregistrer</button>
<a href="liste.php" class="btn-secondaire">Annuler</a>
</form>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body></html>
