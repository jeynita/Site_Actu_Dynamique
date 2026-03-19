
-<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$pdo           = getPDO();
$par_page      = 5;
$page_courante = max(1, (int)($_GET['page'] ?? 1));
$offset        = ($page_courante - 1) * $par_page;

$total    = (int)$pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$nb_pages = (int)ceil($total / $par_page);

$stmt = $pdo->prepare(
    'SELECT a.id, a.titre, a.description_courte, a.date_publication,
            c.nom AS categorie, c.id AS id_categorie,
            CONCAT(u.prenom, " ", u.nom) AS auteur
     FROM articles a
     JOIN categories c ON a.id_categorie = c.id
     JOIN utilisateurs u ON a.id_auteur = u.id
     ORDER BY a.date_publication DESC
     LIMIT :limite OFFSET :offset'
);
$stmt->bindValue(':limite', $par_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$articles   = $stmt->fetchAll();
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil — Site d'Actualité</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>
<?php include __DIR__ . '/entete.php'; ?>
<?php include __DIR__ . '/menu.php'; ?>
<main class="container">
    <div class="filtres">
        <strong>Catégories :</strong>
        <a href="/Site_Actu_Dynamique/accueil.php" class="btn-filtre">Toutes</a>
        <?php foreach ($categories as $cat): ?>
            <a href="/Site_Actu_Dynamique/articles/par_categorie.php?id=<?= $cat['id'] ?>"
               class="btn-filtre">
                <?= htmlspecialchars($cat['nom']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <h1>Dernières actualités</h1>

    <?php if (empty($articles)): ?>
        <p class="vide">Aucun article disponible pour le moment.</p>
    <?php endif; ?>

    <div class="liste-articles">
    <?php foreach ($articles as $a): ?>
        <article class="carte-article">
            <a href="/Site_Actu_Dynamique/articles/par_categorie.php?id=<?= $a['id_categorie'] ?>"
               class="badge-categorie"><?= htmlspecialchars($a['categorie']) ?></a>
            <h2>
                <a href="/Site_Actu_Dynamique/articles/detail.php?id=<?= $a['id'] ?>">
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

    <?php if ($nb_pages > 1): ?>
    <nav class="pagination">
        <?php if ($page_courante > 1): ?>
            <a href="?page=<?= $page_courante - 1 ?>" class="btn-secondaire">&larr; Précédent</a>
        <?php endif; ?>
        <span>Page <?= $page_courante ?> / <?= $nb_pages ?></span>
        <?php if ($page_courante < $nb_pages): ?>
            <a href="?page=<?= $page_courante + 1 ?>" class="btn-secondaire">Suivant &rarr;</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/pied.php'; ?>
</body>
</html>
```
