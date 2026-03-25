<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$pdo           = getPDO();
$par_page      = 6; 
$page_courante = max(1, (int)($_GET['page'] ?? 1));
$offset        = ($page_courante - 1) * $par_page;

$total    = (int)$pdo->query('SELECT COUNT(*) FROM articles WHERE est_supprime = 0')->fetchColumn();
$nb_pages = (int)ceil($total / $par_page);

$stmt = $pdo->prepare(
    'SELECT a.id, a.titre, a.description_courte, a.date_publication, a.image,
            c.nom AS categorie, c.id AS id_categorie,
            CONCAT(u.prenom, " ", u.nom) AS auteur
     FROM articles a
     JOIN categories c ON a.id_categorie = c.id
     JOIN utilisateurs u ON a.id_auteur = u.id
     WHERE a.est_supprime = 0
     ORDER BY a.date_publication DESC
     LIMIT :limite OFFSET :offset'
);
$stmt->bindValue(':limite', $par_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

$article_une = null;
if ($page_courante === 1 && !empty($articles)) {
    $article_une = array_shift($articles); 
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil — XIBAAR YI</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/entete.php'; ?>
<?php include __DIR__ . '/menu.php'; ?>

<main class="container">

    <form action="/Site_Actu_Dynamique/articles/recherche.php" method="GET" class="barre-recherche">
        <input type="text" name="q" placeholder="Rechercher un article..." required>
        <button type="submit">Rechercher</button>
    </form>

    <div class="filter-wrapper">
        <span class="filter-label">Explorer par catégorie :</span>
        <div class="filter-group">
            <a href="/Site_Actu_Dynamique/accueil.php" class="filter-pill <?= !isset($_GET['id']) ? 'active' : '' ?>">Toutes</a>
            <?php foreach ($categories as $cat): ?>
                <?php if ($cat['total_articles'] > 0): ?>
                    <a href="/Site_Actu_Dynamique/articles/par_categorie.php?id=<?= $cat['id'] ?>" 
                       class="filter-pill <?= (isset($_GET['id']) && $_GET['id'] == $cat['id']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['nom']) ?>
                        <span class="pill-count"><?= $cat['total_articles'] ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($article_une): ?>
        <section class="main-featured">
            <div class="featured-badge">À LA UNE</div>
            <div class="featured-flex">
                <div class="featured-img">
                    <?php if (!empty($article_une['image'])): ?>
                        <img src="/Site_Actu_Dynamique/uploads/<?= htmlspecialchars($article_une['image']) ?>" alt="Image à la une">
                    <?php else: ?>
                        <div class="image-placeholder">XIBAAR YI</div>
                    <?php endif; ?>
                </div>
                <div class="featured-text">
                    <span class="cat-tag"><?= htmlspecialchars($article_une['categorie']) ?></span>
                    <h1><?= htmlspecialchars($article_une['titre']) ?></h1>
                    <p><?= htmlspecialchars(mb_strimwidth($article_une['description_courte'], 0, 180, "...")) ?></p>
                    <div class="card-meta">
                        <span>Par <strong><?= htmlspecialchars($article_une['auteur']) ?></strong></span>
                        <time><?= date('d/m/Y', strtotime($article_une['date_publication'])) ?></time>
                    </div>
                    <a href="/Site_Actu_Dynamique/articles/detail.php?id=<?= $article_une['id'] ?>" class="btn-primary">Lire l'article</a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <h1>Dernières actualités</h1>

    <?php if (empty($articles) && !$article_une): ?>
        <p class="vide">Aucun article disponible pour le moment.</p>
    <?php endif; ?>

    <div class="articles-grid">
        <?php foreach ($articles as $a): ?>
            <article class="card">
                <div class="card-image">
                    <?php if (!empty($a['image'])): ?>
                        <img src="/Site_Actu_Dynamique/uploads/<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['titre']) ?>">
                    <?php else: ?>
                        <div class="image-placeholder">XIBAAR YI</div>
                    <?php endif; ?>
                    
                    <a href="/Site_Actu_Dynamique/articles/par_categorie.php?id=<?= $a['id_categorie'] ?>" class="card-badge">
                        <?= htmlspecialchars($a['categorie']) ?>
                    </a>
                </div>

                <div class="card-content">
                    <h2>
                        <a href="/Site_Actu_Dynamique/articles/detail.php?id=<?= $a['id'] ?>">
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
                <a href="?page=<?= $page_courante - 1 ?>" class="prev">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $nb_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page_courante ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page_courante < $nb_pages): ?>
                <a href="?page=<?= $page_courante + 1 ?>" class="next">Suivant</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/pied.php'; ?>

</body>
</html>