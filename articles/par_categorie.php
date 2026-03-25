<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

$pdo = getPDO();

$id_cat = (int)($_GET['id'] ?? 0);

$par_page = 6;
$page_courante = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page_courante - 1) * $par_page;

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->execute([':id' => $id_cat]);
$categorie = $stmt->fetch();

if (!$categorie) {
    header('Location: /Site_Actu_Dynamique/accueil.php');
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE id_categorie = :id_cat AND est_supprime = 0');
$stmt->execute([':id_cat' => $id_cat]);
$total = (int)$stmt->fetchColumn();

$nb_pages = (int)ceil($total / $par_page);

$stmt = $pdo->prepare(
    'SELECT a.id, a.titre, a.description_courte, a.date_publication, a.image, a.id_categorie,
            c.nom AS nom_categorie,
            CONCAT(u.prenom, " ", u.nom) AS auteur
     FROM articles a
     JOIN categories c ON a.id_categorie = c.id
     JOIN utilisateurs u ON a.id_auteur = u.id
     WHERE a.id_categorie = :id_cat AND a.est_supprime = 0
     ORDER BY a.date_publication DESC
     LIMIT :limite OFFSET :offset'
);

$stmt->bindValue(':id_cat', $id_cat, PDO::PARAM_INT);
$stmt->bindValue(':limite', $par_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$articles = $stmt->fetchAll();

$categories = $pdo->query(
    'SELECT c.id, c.nom, COUNT(a.id) as total_articles
     FROM categories c
     LEFT JOIN articles a ON a.id_categorie = c.id AND a.est_supprime = 0
     GROUP BY c.id, c.nom
     ORDER BY c.nom'
)->fetchAll();
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

    <div class="filter-wrapper">
        <span class="filter-label">Catégories :</span>
        <div class="filter-group">
            <a href="/Site_Actu_Dynamique/accueil.php" class="filter-pill">Toutes</a>
            <?php foreach ($categories as $cat): ?>
                <?php if ($cat['total_articles'] > 0): ?>
                    <a href="par_categorie.php?id=<?= $cat['id'] ?>" 
                       class="filter-pill <?= $cat['id'] == $id_cat ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['nom']) ?> 
                        <span class="pill-count"><?= $cat['total_articles'] ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <h1>Catégorie : <?= htmlspecialchars($categorie['nom']) ?></h1>
    <p class="compteur"><?= $total ?> article(s) trouvé(s)</p>

    <div class="articles-grid">
        <?php if (empty($articles)): ?>
            <p class="vide">Aucun article dans cette catégorie pour le moment.</p>
        <?php endif; ?>

        <?php foreach ($articles as $a): ?>
            <article class="card">
                <div class="card-image">
                    <?php if (!empty($a['image'])): ?>
                        <img src="/Site_Actu_Dynamique/uploads/<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['titre']) ?>">
                    <?php else: ?>
                        <div class="image-placeholder">XIBAAR YI</div>
                    <?php endif; ?>
                    
                    <span class="card-badge">
                        <?= htmlspecialchars($a['nom_categorie']) ?>
                    </span>
                </div>

                <div class="card-content">
                    <h2>
                        <a href="detail.php?id=<?= $a['id'] ?>">
                            <?= htmlspecialchars(mb_strimwidth($a['titre'], 0, 60, "...")) ?>
                        </a>
                    </h2>

                    <p class="card-excerpt">
                        <?= htmlspecialchars(mb_strimwidth($a['description_courte'], 0, 100, "...")) ?>
                    </p>

                    <footer class="card-meta">
                        <span>Par <strong><?= htmlspecialchars($a['auteur']) ?></strong></span>
                        <time><?= date('d/m/Y', strtotime($a['date_publication'])) ?></time>
                    </footer>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($nb_pages > 1): ?>
        <nav class="pagination">
            <?php if ($page_courante > 1): ?>
                <a href="?id=<?= $id_cat ?>&page=<?= $page_courante - 1 ?>" class="prev">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $nb_pages; $i++): ?>
                <a href="?id=<?= $id_cat ?>&page=<?= $i ?>" class="<?= $i == $page_courante ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page_courante < $nb_pages): ?>
                <a href="?id=<?= $id_cat ?>&page=<?= $page_courante + 1 ?>" class="next">Suivant</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/../pied.php'; ?>

</body>
</html>