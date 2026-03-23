<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
$pdo = getPDO();
$id_cat = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute([':id' => $id_cat]);
$categorie = $stmt->fetch();
if (!$categorie) {
header('Location: /.../accueil.php');
exit;
}
$stmt = $pdo->prepare(
'SELECT a.id, a.titre, a.description_courte, a.date_publication,
CONCAT(u.prenom, " ", u.nom) AS auteur
FROM articles a
JOIN utilisateurs u ON a.id_auteur = u.id
WHERE a.id_categorie = :id_cat
ORDER BY a.date_publication DESC'
);
$stmt->execute([':id_cat' => $id_cat]);
$articles = $stmt->fetchAll();
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Categorie : <?= htmlspecialchars($categorie['nom']) ?></title>
<link rel="stylesheet" href="/.../css/style.css">
</head>
<body>
<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>
<main class="container">
<div class="filtres">
<strong>Categories :</strong>
<a href="/.../accueil.php" class="btn-filtre">Toutes</a>
<?php foreach ($categories as $cat): ?>
<a href="par_categorie.php?id=<?= $cat['id'] ?>"
class="btn-filtre <?= $cat['id'] == $id_cat ? 'actif' : '' ?>">
<?= htmlspecialchars($cat['nom']) ?>
</a>
<?php endforeach; ?>
</div>
<h1>Categorie : <?= htmlspecialchars($categorie['nom']) ?></h1>
<p class="compteur"><?= count($articles) ?> article(s)</p>
<div class="liste-articles">
<?php if (empty($articles)): ?>
<p class="vide">Aucun article dans cette categorie.</p>
<?php endif; ?>
<?php foreach ($articles as $a): ?>
<article class="carte-article">
<h2>
<a href="detail.php?id=<?= $a['id'] ?>">
<?= htmlspecialchars($a['titre']) ?>
</a>
</h2>
<p class="description"><?= htmlspecialchars($a['description_courte']) ?></p>
<footer class="meta-article">
Par <strong><?= htmlspecialchars($a['auteur']) ?></strong>
&mdash; <?= date('d/m/Y', strtotime($a['date_publication'])) ?>
</footer>
</article>
<?php endforeach; ?>
</div>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>