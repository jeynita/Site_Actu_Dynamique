<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']);
$pdo = getPDO(); $erreurs = [];
$d = ['nom'=>'','prenom'=>'','login'=>'','role'=>'editeur'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$d['nom'] = trim($_POST['nom'] ?? '');
$d['prenom'] = trim($_POST['prenom'] ?? '');
$d['login'] = trim($_POST['login'] ?? '');
$d['role'] = $_POST['role'] ?? 'editeur';
$mdp = $_POST['mot_de_passe'] ?? '';
$mdp2 = $_POST['mdp_confirm'] ?? '';
if (empty($d['nom'])) $erreurs[] = 'Le nom est obligatoire.';
if (empty($d['prenom'])) $erreurs[] = 'Le prenom est obligatoire.';
if (empty($d['login'])) $erreurs[] = 'Le login est obligatoire.';
if (strlen($mdp) < 6) $erreurs[] = 'Mot de passe : 6 caracteres minimum.';
if ($mdp !== $mdp2) $erreurs[] = 'Les mots de passe ne correspondent pas.';
if (empty($erreurs)) {
$stmt = $pdo->prepare(
'INSERT INTO utilisateurs (nom,prenom,login,mot_de_passe,role)
VALUES (:nom,:prenom,:login,:mdp,:role)'
);
$stmt->execute([':nom'=>$d['nom'],':prenom'=>$d['prenom'],
':login'=>$d['login'],':mdp'=>password_hash($mdp,PASSWORD_BCRYPT),
':role'=>$d['role']]);
header('Location: liste.php'); exit;
}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajouter utilisateur</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Ajouter un utilisateur</h1>
<?php foreach ($erreurs as $e): ?>
<div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form id="fUser" method="post" novalidate>
<div class="champ"><label>Nom *</label>
<input type="text" name="nom" value="<?= htmlspecialchars($d['nom']) ?>"></div>
<div class="champ"><label>Prenom *</label>
<input type="text" name="prenom" value="<?= htmlspecialchars($d['prenom']) ?>"></div>
<div class="champ"><label>Login *</label>
<input type="text" name="login" value="<?= htmlspecialchars($d['login']) ?>"></div>
<div class="champ">
<label>Mot de passe * (min. 6 caracteres)</label>
<input type="password" id="mdp" name="mot_de_passe">
<span class="erreur-js" id="err-mdp"></span>
</div>
<div class="champ">
<label>Confirmer le mot de passe *</label>
<input type="password" id="mdp2" name="mdp_confirm">
<span class="erreur-js" id="err-mdp2"></span>
</div>
<div class="champ"><label>Role</label>
<select name="role">
<option value="editeur" <?= $d['role']==='editeur'?'selected':'' ?>>Editeur</option>
<option value="administrateur" <?= $d['role']==='administrateur'?'selected':''
?>>Administrateur</option>
</select></div>
<button type="submit" class="btn-primaire">Creer</button>
<a href="liste.php" class="btn-secondaire">Annuler</a>
</form>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
<script>
document.getElementById('fUser').addEventListener('submit', function(e) {
let ok = true;
const mdp = document.getElementById('mdp').value;
const mdp2 = document.getElementById('mdp2').value;
document.getElementById('err-mdp').textContent = '';
document.getElementById('err-mdp2').textContent = '';
if (mdp.length < 6) {
document.getElementById('err-mdp').textContent = 'Minimum 6 caracteres.'; ok = false;
}
if (mdp !== mdp2) {
document.getElementById('err-mdp2').textContent = 'Les mots de passe ne correspondent pas.'; ok
= false;
}
if (!ok) e.preventDefault();
});
</script>
</body></html>