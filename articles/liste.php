<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO();
$stmt = $pdo->query(
'SELECT a.id, a.titre, a.date_publication, c.nom AS categorie,
CONCAT(u.prenom, " ", u.nom) AS auteur
FROM articles a
JOIN categories c ON a.id_categorie = c.id
JOIN utilisateurs u ON a.id_auteur = u.id
ORDER BY a.date_publication DESC'
);
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
<a href="ajouter.php" class="btn-primaire">+ Nouvel article</a>
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
<a href="supprimer.php?id=<?= $a['id'] ?>" class="btn-danger"
onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>