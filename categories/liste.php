<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

autoriser(['editeur', 'administrateur']);

$pdo = getPDO();


$sql = "SELECT c.*, COUNT(a.id) as nb_articles 
        FROM categories c 
        LEFT JOIN articles a ON c.id = a.id_categorie AND a.est_supprime = 0
        GROUP BY c.id 
        ORDER BY c.nom ASC";

$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Catégories </title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>

<main class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Liste des Catégories</h1>
        <a href="ajouter.php" class="btn-primaire">+ Nouvelle Catégorie</a>
    </div>

    <?php if (isset($_GET['erreur']) && $_GET['erreur'] == 'pas_vide'): ?>
        <div class="alerte erreur">Impossible de supprimer : cette catégorie contient des articles actifs.</div>
    <?php endif; ?>

    <table class="tableau">
        <thead>
            <tr>
                <th>Nom de la catégorie</th>
                <th style="text-align: center;">Nombre d'articles</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><strong><?= htmlspecialchars($c['nom']) ?></strong></td>
                <td style="text-align: center;">
                    <span class="badge-categorie"><?= $c['nb_articles'] ?> article(s)</span>
                </td>
                <td>
                    <div class="actions-group" style="justify-content: center;">
                        <a href="modifier.php?id=<?= $c['id'] ?>" class="btn-edit">Modifier</a>
                        
                        <?php if ($c['nb_articles'] == 0): ?>
                            <a href="supprimer.php?id=<?= $c['id'] ?>" class="btn-delete" 
                            onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
                        <?php else: ?>
                            <span style="color: #9ca3af; font-size: 0.8rem; font-style: italic;">(Non vide)</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>