<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

$pdo           = getPDO();
$par_page      = 5;
$page_courante = max(1, (int)($_GET['page'] ?? 1));
$offset        = ($page_courante - 1) * $par_page;

// Total articles
$total    = (int)$pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$nb_pages = (int)ceil($total / $par_page);

// Récupération articles
$stmt = $pdo->prepare(
    'SELECT a.id, a.titre, a.description_courte, a.date_publication,
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

// Catégories avec compteur
$categories = $pdo->query(
    'SELECT c.id, c.nom, COUNT(a.id) as total_articles
     FROM categories c
     LEFT JOIN articles a ON a.id_categorie = c.id
     GROUP BY c.id, c.nom
     ORDER BY c.nom'
)->fetchAll();
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

    <!-- Barre de recherche -->
    <form action="/Site_Actu_Dynamique/articles/recherche.php" method="GET" class="barre-recherche">
        <input type="text" name="q" placeholder="Rechercher un article..." required>
        <button type="submit">Rechercher</button>
    </form>

    <!-- Catégories -->
    <div class="filtres">
        <strong>Catégories :</strong>
        <a href="/Site_Actu_Dynamique/accueil.php" class="btn-filtre">Toutes</a>

        <?php foreach ($categories as $cat): ?>
            <a href="/Site_Actu_Dynamique/articles/par_categorie.php?id=<?= $cat['id'] ?>"
               class="btn-filtre">
                <?= htmlspecialchars($cat['nom']) ?> (<?= $cat['total_articles'] ?>)
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
                   class="badge-categorie">
                    <?= htmlspecialchars($a['categorie']) ?>
                </a>

                <h2>
                    <a href="/Site_Actu_Dynamique/articles/detail.php?id=<?= $a['id'] ?>">
                        <?= htmlspecialchars($a['titre']) ?>
                    </a>
                </h2>

                <p class="description">
                    <?= htmlspecialchars($a['description_courte']) ?>
                </p>

                <footer class="meta-article">
                    Par <strong><?= htmlspecialchars($a['auteur']) ?></strong>
                    — <?= date('d/m/Y', strtotime($a['date_publication'])) ?>
                </footer>

            </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination numérotée -->
    <?php if ($nb_pages > 1): ?>
        <nav class="pagination">

            <?php if ($page_courante > 1): ?>
                <a href="?page=<?= $page_courante - 1 ?>" class="btn-secondaire">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $nb_pages; $i++): ?>
                <a href="?page=<?= $i ?>"
                   class="<?= $i == $page_courante ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page_courante < $nb_pages): ?>
                <a href="?page=<?= $page_courante + 1 ?>" class="btn-secondaire">Suivant</a>
            <?php endif; ?>

        </nav>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/pied.php'; ?>

</body>
</html>