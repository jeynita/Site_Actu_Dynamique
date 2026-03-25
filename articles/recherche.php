
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';


$pdo = getPDO();
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$articles = [];

if (!empty($search)) {
    try {
        $sql = "SELECT a.*, c.nom AS categorie, CONCAT(u.prenom, ' ', u.nom) AS auteur 
                FROM articles a 
                JOIN categories c ON a.id_categorie = c.id 
                JOIN utilisateurs u ON a.id_auteur = u.id 
                WHERE (a.titre LIKE :q1 OR a.contenu LIKE :q2) 
                AND a.est_supprime = 0
                ORDER BY a.date_publication DESC";

        $stmt = $pdo->prepare($sql);
        $param = "%$search%";

        $stmt->execute([
            ':q1' => $param,
            ':q2' => $param
        ]);
        
        $articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        $erreur_sql = "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche : <?= htmlspecialchars($search) ?></title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>

<main class="container">
    <h1>Résultats de recherche</h1>

    <?php if (isset($erreur_sql)): ?>
        <div class="alerte erreur"><?= htmlspecialchars($erreur_sql) ?></div>
    <?php endif; ?>

    <section class="recherche-container">
        <form action="recherche.php" method="GET" class="barre-recherche">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher..." required>
            <button type="submit">Rechercher</button>
        </form>
    </section>

    <?php if (empty($search)): ?>
        <p class="vide">Saisissez un mot-clé.</p>
    <?php elseif (empty($articles)): ?>
        <p class="vide">Aucun résultat pour "<strong><?= htmlspecialchars($search) ?></strong>".</p>
    <?php else: ?>
        <p class="compteur"><?= count($articles) ?> résultat(s)</p>
        
        <div class="liste-articles">
            <?php foreach ($articles as $a): ?>
                <article class="carte-article">
                    <span class="badge-categorie"><?= htmlspecialchars($a['categorie']) ?></span>
                    
                    <h2>
                        <a href="detail.php?id=<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['titre']) ?>
                        </a>
                    </h2>
                    
                    <?php if (!empty($a['image'])): ?>
                        <img src="/Site_Actu_Dynamique/uploads/<?= htmlspecialchars($a['image']) ?>" alt="" class="article-image-mini">
                    <?php endif; ?>

                    <p class="description">
                        <?= htmlspecialchars(mb_strimwidth(strip_tags($a['contenu']), 0, 160, "...")) ?>
                    </p>

                    <footer class="meta-article">
                        Par <strong><?= htmlspecialchars($a['auteur']) ?></strong> 
                        le <?= date('d/m/Y', strtotime($a['date_publication'])) ?>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 2rem;">
        <a href="../accueil.php" class="btn-secondaire">Retour</a>
    </div>
</main>

<?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>