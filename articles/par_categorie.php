<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

$pdo = getPDO();

$id_cat = (int)($_GET['id'] ?? 0);

$par_page = 5;
$page_courante = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page_courante - 1) * $par_page;

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute([':id' => $id_cat]);
$categorie = $stmt->fetch();

if (!$categorie) {
    header('Location: /Site_Actu_Dynamique/accueil.php');
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE id_categorie = :id_cat');
$stmt->execute([':id_cat' => $id_cat]);
$total = (int)$stmt->fetchColumn();

$nb_pages = (int)ceil($total / $par_page);

$stmt = $pdo->prepare(
    'SELECT a.id, a.titre, a.description_courte, a.date_publication,
            CONCAT(u.prenom, " ", u.nom) AS auteur
     FROM articles a
     JOIN utilisateurs u ON a.id_auteur = u.id
     WHERE a.id_categorie = :id_cat
     ORDER BY a.date_publication DESC
     LIMIT :limite OFFSET :offset'
);

$stmt->bindValue(':id_cat', $id_cat, PDO::PARAM_INT);
$stmt->bindValue(':limite', $par_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$articles = $stmt->fetchAll();

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Catégorie : <?= htmlspecialchars($categorie['nom']) ?></title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>

<main class="container">

<div class="filtres">
<strong>Catégories :</strong>
<a href="/Site_Actu_Dynamique/accueil.php" class="btn-filtre">Toutes</a>

<?php foreach ($categories as $cat): ?>
<a href="par_categorie.php?id=<?= $cat['id'] ?>"
class="btn-filtre <?= $cat['id'] == $id_cat ? 'actif' : '' ?>">
<?= htmlspecialchars($cat['nom']) ?>
</a>
<?php endforeach; ?>
</div>

<h1>Catégorie : <?= htmlspecialchars($categorie['nom']) ?></h1>

<p class="compteur"><?= $total ?> article(s)</p>

<div class="liste-articles">

<?php if (empty($articles)): ?>
<p class="vide">Aucun article dans cette catégorie.</p>
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
— <?= date('d/m/Y', strtotime($a['date_publication'])) ?>
</footer>

</article>
<?php endforeach; ?>

</div>

<?php if ($nb_pages > 1): ?>
<nav class="pagination">

<?php if ($page_courante > 1): ?>
<a href="?id=<?= $id_cat ?>&page=<?= $page_courante - 1 ?>" class="btn-secondaire">Précédent</a>
<?php endif; ?>

<?php for ($i = 1; $i <= $nb_pages; $i++): ?>
<a href="?id=<?= $id_cat ?>&page=<?= $i ?>"
class="<?= $i == $page_courante ? 'active' : '' ?>">
<?= $i ?>
</a>
<?php endfor; ?>

<?php if ($page_courante < $nb_pages): ?>
<a href="?id=<?= $id_cat ?>&page=<?= $page_courante + 1 ?>" class="btn-secondaire">Suivant</a>
<?php endif; ?>

</nav>
<?php endif; ?>

</main>

<?php include __DIR__ . '/../pied.php'; ?>

</body>
</html>