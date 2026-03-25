<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: liste.php'); 
    exit;
}

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $id_cat = (int)($_POST['id_categorie'] ?? 0);

    if (empty($titre)) $erreurs[] = 'Le titre est obligatoire.';
    if (empty($description)) $erreurs[] = 'La description courte est obligatoire.';
    if (empty($contenu)) $erreurs[] = 'Le contenu est obligatoire.';
    if ($id_cat <= 0) $erreurs[] = 'Veuillez choisir une catégorie.';

    $nom_image = $article['image']; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $dossier_upload = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        
        if (!is_dir($dossier_upload)) {
            mkdir($dossier_upload, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $extensions_autorisees)) {
            $erreurs[] = 'Format d\'image non autorisé (JPG, PNG, GIF, WEBP).';
        } else {
            $nouveau_nom = uniqid('art_', true) . '.' . $extension;
            $chemin_destination = $dossier_upload . $nouveau_nom;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $chemin_destination)) {
                if (!empty($article['image']) && file_exists($dossier_upload . $article['image'])) {
                    unlink($dossier_upload . $article['image']);
                }
                $nom_image = $nouveau_nom;
            } else {
                $erreurs[] = 'Erreur technique lors de l\'upload de la nouvelle image.';
            }
        }
    }

    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare('
                UPDATE articles 
                SET titre = :titre, description_courte = :desc, contenu = :contenu, 
                    id_categorie = :cat, image = :img
                WHERE id = :id
            ');
            $stmt->execute([
                ':titre'   => $titre,
                ':desc'    => $description,
                ':contenu' => $contenu,
                ':cat'     => $id_cat,
                ':img'     => $nom_image,
                ':id'      => $id
            ]);
            $succes = true;
            $article['image'] = $nom_image;
        } catch (PDOException $e) {
            $erreurs[] = "Erreur SQL : " . $e->getMessage();
        }
    }
} else {
    $titre = $article['titre'];
    $description = $article['description_courte'];
    $contenu = $article['contenu'];
    $id_cat = $article['id_categorie'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'article</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../entete.php'; ?>
<?php include __DIR__ . '/../menu.php'; ?>

<main class="container">
    <h1>Modifier l'article</h1>

    <?php if ($succes): ?>
        <div class="alerte succes">
            L'article a été mis à jour avec succès ! 
            <a href="detail.php?id=<?= $id ?>">Voir l'article</a> ou <a href="liste.php">Retour à la liste</a>
        </div>
    <?php endif; ?>

    <?php foreach ($erreurs as $e): ?>
        <div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <div class="form-wrapper">
        <form method="POST" enctype="multipart/form-data">
            <div class="champ">
                <label>Titre *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($titre) ?>" required>
            </div>

            <div class="champ">
                <label>Catégorie *</label>
                <select name="id_categorie" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($id_cat == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="champ">
                <label>Image actuelle</label>
                <?php if (!empty($article['image'])): ?>
                    <div style="margin: 10px 0;">
                        <img src="/Site_Actu_Dynamique/uploads/<?= htmlspecialchars($article['image']) ?>" alt="Aperçu" style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    </div>
                <?php else: ?>
                    <p style="font-style: italic; color: #888;">Aucune image pour cet article.</p>
                <?php endif; ?>
                <label>Remplacer l'image (optionnel)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="champ">
                <label>Description courte *</label>
                <textarea name="description_courte" rows="3" required><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="champ">
                <label>Contenu complet *</label>
                <textarea name="contenu" rows="10" required><?= htmlspecialchars($contenu) ?></textarea>
            </div>

            <div class="actions" style="margin-top: 20px;">
                <button type="submit" class="btn-primaire">Enregistrer les modifications</button>
                <a href="liste.php" class="btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../pied.php'; ?>

</body>
</html>