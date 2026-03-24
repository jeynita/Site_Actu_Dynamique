<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

$pdo  = getPDO();
$id   = (int)($_GET['id'] ?? 0);

// MODIFICATION 1 : Ajout de la condition 'est_supprime = 0'
$stmt = $pdo->prepare(
    'SELECT a.*, c.nom AS categorie, CONCAT(u.prenom, " ", u.nom) AS auteur
     FROM articles a
     JOIN categories c ON a.id_categorie = c.id
     JOIN utilisateurs u ON a.id_auteur = u.id
     WHERE a.id = :id AND a.est_supprime = 0' 
);
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

// MODIFICATION 2 : Si l'article n'existe pas OU est archivé
if (!$article) {
    // Redirection avec un petit message d'erreur dans l'URL (optionnel)
    header('Location: /Site_Actu_Dynamique/accueil.php?erreur=introuvable');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['titre']) ?> - ESP Actu</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>

<main class="container">

    <a href="/Site_Actu_Dynamique/accueil.php" class="lien-retour" style="display: inline-block; margin-bottom: 1.5rem;">
        &larr; Retour à l'accueil
    </a>

    <article class="article-complet">

        <span class="badge-categorie">
            <?= htmlspecialchars($article['categorie']) ?>
        </span>

        <h1><?= htmlspecialchars($article['titre']) ?></h1>

        <div class="meta-article">
            Par <strong><?= htmlspecialchars($article['auteur']) ?></strong>
            — Publié le <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?>
        </div>

        <?php if (!empty($article['image'])): ?>
            <div class="image-container-detail">
                <img src="/Site_Actu_Dynamique/uploads/<?= htmlspecialchars($article['image']) ?>" 
                     class="article-image" 
                     alt="<?= htmlspecialchars($article['titre']) ?>">
            </div>
        <?php endif; ?>

        <div class="contenu-article">
            <?= nl2br(htmlspecialchars($article['contenu'])) ?>
        </div>

    </article>

</main>

<?php include __DIR__ . '/../pied.php'; ?>

</body>
</html>