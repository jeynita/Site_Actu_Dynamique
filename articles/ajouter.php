<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['editeur', 'administrateur']);
$pdo = getPDO();
$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
$erreurs = [];
$d = ['titre'=>'','description_courte'=>'','contenu'=>'','id_categorie'=>0];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$d['titre'] = trim($_POST['titre'] ?? '');
$d['description_courte'] = trim($_POST['description_courte'] ?? '');
$d['contenu'] = trim($_POST['contenu'] ?? '');
$d['id_categorie'] = (int)($_POST['id_categorie'] ?? 0);
if (empty($d['titre'])) $erreurs[] = 'Le titre est obligatoire.';
if (empty($d['description_courte'])) $erreurs[] = 'La description courte est obligatoire.';
if (empty($d['contenu'])) $erreurs[] = 'Le contenu est obligatoire.';
if ($d['id_categorie'] <= 0) $erreurs[] = 'Veuillez choisir une categorie.';
if (empty($erreurs)) {
$stmt = $pdo->prepare(
'INSERT INTO articles (titre, description_courte, contenu, id_categorie, id_auteur)
VALUES (:titre, :desc, :contenu, :cat, :auteur)'
);
$stmt->execute([
':titre' => $d['titre'],
':desc' => $d['description_courte'],
':contenu' => $d['contenu'],
':cat' => $d['id_categorie'],
':auteur' => $_SESSION['utilisateur']['id'],
]);
header('Location: liste.php');
exit;
}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><title>Ajouter un article</title>
<link rel="stylesheet" href="/.../css/style.css">
</head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Ajouter un article</h1>
<?php foreach ($erreurs as $e): ?>
<div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form id="formArticle" method="post" novalidate>
<div class="champ">
<label for="titre">Titre *</label>
<input type="text" id="titre" name="titre" value="<?= htmlspecialchars($d['titre']) ?>">
<span class="erreur-js" id="err-titre"></span>
</div>
<div class="champ">
<label for="description_courte">Description courte *</label>
<textarea id="description_courte" name="description_courte" rows="3"><?=
htmlspecialchars($d['description_courte']) ?></textarea>
<span class="erreur-js" id="err-desc"></span>
</div>
<div class="champ">
<label for="contenu">Contenu *</label>
<textarea id="contenu" name="contenu" rows="10"><?= htmlspecialchars($d['contenu'])
?></textarea>
<span class="erreur-js" id="err-contenu"></span>
</div>
<div class="champ">
<label for="id_categorie">Categorie *</label>
<select id="id_categorie" name="id_categorie">
<option value="">-- Choisir --</option>
<?php foreach ($categories as $cat): ?>
<option value="<?= $cat['id'] ?>" <?= $d['id_categorie']==$cat['id']?'selected':'' ?>>
<?= htmlspecialchars($cat['nom']) ?>
</option>
<?php endforeach; ?>
</select>
<span class="erreur-js" id="err-cat"></span>
</div>
<button type="submit" class="btn-primaire">Publier</button>
<a href="liste.php" class="btn-secondaire">Annuler</a>
</form>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
<script>
document.getElementById('formArticle').addEventListener('submit', function(e) {
let ok = true;
[['titre','err-titre','Le titre est obligatoire.'],
['description_courte','err-desc','La description est obligatoire.'],
['contenu','err-contenu','Le contenu est obligatoire.'],
['id_categorie','err-cat','Veuillez choisir une categorie.']
].forEach(([id,errId,msg]) => {
document.getElementById(errId).textContent = '';
if (!document.getElementById(id).value.trim()) {
document.getElementById(errId).textContent = msg; ok = false;
}
});
if (!ok) e.preventDefault();
});
</script>
</body></html>