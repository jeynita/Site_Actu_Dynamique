<?php
// 1. Inclusions avec chemins relatifs robustes
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// 2. Vérification des droits via la fonction définie dans session.php
autoriser(['editeur', 'administrateur']);

$pdo = getPDO();
$erreurs = [];
$succes = false;

// Récupération de l'ID de l'auteur depuis la session
$id_auteur = $_SESSION['utilisateur']['id'] ?? null;

// 3. Préparation du dossier d'upload
$upload_dir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 4. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);
    $nom_image = null;

    if (empty($titre) || empty($description) || empty($contenu) || $id_categorie <= 0) {
        $erreurs[] = "Tous les champs obligatoires (*) doivent être remplis.";
    }

    // Gestion de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $extensions_valides = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $extensions_valides)) {
            $erreurs[] = "Format d'image non supporté.";
        } else {
            // uniqid pour éviter les écrasements de fichiers
            $nom_image = uniqid('art_', true) . '.' . $extension;
            $destination = $upload_dir . $nom_image;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $erreurs[] = "Erreur lors du transfert de l'image.";
                $nom_image = null;
            }
        }
    }

    // Insertion en base de données
    if (empty($erreurs)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO articles (titre, description_courte, contenu, id_categorie, id_auteur, image, date_publication) 
                VALUES (:titre, :desc, :cont, :cat, :auteur, :img, NOW())
            ");
            $stmt->execute([
                ':titre'  => $titre,
                ':desc'   => $description,
                ':cont'   => $contenu,
                ':cat'    => $id_categorie,
                ':auteur' => $id_auteur,
                ':img'    => $nom_image
            ]);
            $succes = true;
        } catch (PDOException $e) {
            $erreurs[] = "Erreur SQL : " . $e->getMessage();
        }
    }
}

// Récupération des catégories pour le select
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un article</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
    <style>
        /* Correction immédiate pour les images trop grosses si elles sont affichées ici */
        .img-preview {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            border-radius: 8px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../entete.php'; ?>
    <?php include __DIR__ . '/../menu.php'; ?>

    <main class="container">
        <h1>Ajouter un nouvel article</h1>

        <?php if ($succes): ?>
            <div class="alerte succes">
                L'article a été publié avec succès ! 
                <a href="/Site_Actu_Dynamique/accueil.php">Retour à l'accueil</a>
            </div>
        <?php endif; ?>

        <?php foreach ($erreurs as $err): ?>
            <div class="alerte erreur"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <form action="ajouter.php" method="POST" enctype="multipart/form-data">
            <div class="champ">
                <label>Titre *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
            </div>

            <div class="champ">
                <label>Catégorie *</label>
                <select name="id_categorie" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_POST['id_categorie']) && $_POST['id_categorie'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="champ">
                <label>Image d'illustration (JPG, PNG, WEBP)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="champ">
                <label>Description courte *</label>
                <textarea name="description_courte" rows="3" required><?= htmlspecialchars($_POST['description_courte'] ?? '') ?></textarea>
            </div>

            <div class="champ">
                <label>Contenu de l'article *</label>
                <textarea name="contenu" rows="10" required><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primaire">Publier l'article</button>
                <a href="/Site_Actu_Dynamique/accueil.php" class="btn-secondaire">Annuler</a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/../pied.php'; ?>
</body>
</html>