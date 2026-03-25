<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO();


$articlesParPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $articlesParPage;


$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$totalPages = ceil($totalArticles / $articlesParPage);


$stmt = $pdo->prepare(
    'SELECT a.id, a.titre, a.date_publication, c.nom AS categorie,
    CONCAT(u.prenom, " ", u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.id_categorie = c.id
    JOIN utilisateurs u ON a.id_auteur = u.id
    WHERE a.est_supprime = 0
    ORDER BY a.date_publication DESC
    LIMIT :limit OFFSET :offset'
);
$stmt->bindValue(':limit', $articlesParPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des articles</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>
<main class="container">
    <h1>Gestion des articles</h1>
    <a href="ajouter.php" class="btn-primaire" style="margin-bottom: 20px; display: inline-block;">+ Nouvel article</a>
    
    <table class="tableau">
        <thead>
            <tr><th>Titre</th><th>Categorie</th><th>Auteur</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['titre']) ?></td>
                <td><?= htmlspecialchars($a['categorie']) ?></td>
                <td><?= htmlspecialchars($a['auteur']) ?></td>
                <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                <td>
                    <a href="modifier.php?id=<?= $a['id'] ?>" class="btn-secondaire">Modifier</a>
                    <a href="supprimer.php?id=<?= $a['id'] ?>" class="btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="prev">« Précédent</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="next">Suivant »</a>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>