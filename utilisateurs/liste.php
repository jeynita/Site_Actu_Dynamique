<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']);
$pdo = getPDO();
$users = $pdo->query('SELECT id, nom, prenom, login, role FROM utilisateurs ORDER BY
nom')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Utilisateurs</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Gestion des utilisateurs</h1>
<a href="ajouter.php" class="btn-primaire">+ Nouvel utilisateur</a>
<table class="tableau">
<thead><tr><th>Nom</th><th>Prenom</th><th>Login</th><th>Role</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr>
<td><?= htmlspecialchars($u['nom']) ?></td>
<td><?= htmlspecialchars($u['prenom']) ?></td>
<td><?= htmlspecialchars($u['login']) ?></td>
<td><?= htmlspecialchars($u['role']) ?></td>
<td>
<a href="modifier.php?id=<?= $u['id'] ?>" class="btn-secondaire">Modifier</a>
<?php if ($u['id'] != $_SESSION['utilisateur']['id']): ?>
<a href="supprimer.php?id=<?= $u['id'] ?>" class="btn-danger"
onclick="return confirm('Supprimer ?')">Supprimer</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body></html>