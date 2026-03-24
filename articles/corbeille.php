<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Sécurité : Seuls les admins ou éditeurs accèdent à la corbeille
autoriser(['editeur', 'administrateur']);

$pdo = getPDO();

// Requête pour récupérer uniquement les articles ARCHIVÉS (est_supprime = 1)
$stmt = $pdo->query('
    SELECT a.id, a.titre, a.date_publication, c.nom AS categorie,
    CONCAT(u.prenom, " ", u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.id_categorie = c.id
    JOIN utilisateurs u ON a.id_auteur = u.id
    WHERE a.est_supprime = 1
    ORDER BY a.date_publication DESC
');
$archives = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Corbeille - ESP Actu</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>

<main class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Articles Archivés (Corbeille)</h1>
        <a href="liste.php" class="btn-secondaire">← Retour à la gestion</a>
    </div>

    <?php if (empty($archives)): ?>
        <p class="vide">La corbeille est vide.</p>
    <?php else: ?>
        <table class="tableau">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Auteur</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archives as $a): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($a['titre']) ?></strong></td>
                    <td><span class="cat-tag"><?= htmlspecialchars($a['categorie']) ?></span></td>
                    <td><?= htmlspecialchars($a['auteur']) ?></td>
                    <td>
                        <div class="actions-group">
                            <a href="restaurer.php?id=<?= $a['id'] ?>" class="btn-edit" style="background: #dcfce7; color: #166534; border-color: #86efac;">
                                Restaurer
                            </a>
                            <a href="supprimer_definitivement.php?id=<?= $a['id'] ?>" class="btn-delete" 
                               onclick="return confirm('Attention : cette action est irréversible. Supprimer définitivement ?')">
                                Détruire
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>