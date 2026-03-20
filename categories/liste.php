<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO();
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Categories</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Gestion des categories</h1>
<a href="ajouter.php" class="btn-primaire">+ Nouvelle categorie</a>
<table class="tableau">
<thead><tr><th>Nom</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($categories as $c): ?>
<tr>
<td><?= htmlspecialchars($c['nom']) ?></td>
<td>
<a href="modifier.php?id=<?= $c['id'] ?>" class="btn-secondaire">Modifier</a>
<a href="supprimer.php?id=<?= $c['id'] ?>" class="btn-danger"
onclick="return confirm('Supprimer ?')">Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body></html>