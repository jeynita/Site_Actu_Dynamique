<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
autoriser(['administrateur']);
$pdo = getPDO(); $id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = :id');
$stmt->execute([':id' => $id]); $user = $stmt->fetch();
if (!$user) { header('Location: liste.php'); exit; }
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$login = trim($_POST['login'] ?? '');
$role = $_POST['role'] ?? 'editeur';
$mdp = $_POST['mot_de_passe'] ?? '';
if (empty($nom)) $erreurs[] = 'Le nom est obligatoire.';
if (empty($prenom)) $erreurs[] = 'Le prenom est obligatoire.';
if (empty($login)) $erreurs[] = 'Le login est obligatoire.';
if (empty($erreurs)) {
if (!empty($mdp)) {
$stmt = $pdo->prepare('UPDATE utilisateurs SET
nom=:nom,prenom=:prenom,login=:login,role=:role,mot_de_passe=:mdp WHERE id=:id');
$stmt->execute([':nom'=>$nom,':prenom'=>$prenom,':login'=>$login,':role'=>$role,
':mdp'=>password_hash($mdp,PASSWORD_BCRYPT),':id'=>$id]);
} else {
$stmt = $pdo->prepare('UPDATE utilisateurs SET nom=:nom,prenom=:prenom,login=:login,role=:role
WHERE id=:id');
$stmt->execute([':nom'=>$nom,':prenom'=>$prenom,':login'=>$login,':role'=>$role,':id'=>$id]);
}
header('Location: liste.php'); exit;
}
} else {
$nom=$user['nom']; $prenom=$user['prenom']; $login=$user['login']; $role=$user['role'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Modifier utilisateur</title>
<link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css"></head>
<body>
<?php include __DIR__ . '/../entete.php'; include __DIR__ . '/../menu.php'; ?>
<main class="container">
<h1>Modifier l'utilisateur</h1>
<?php foreach ($erreurs as $e): ?>
<div class="alerte erreur"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form method="post">
<div class="champ"><label>Nom *</label>
<input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>"></div>
<div class="champ"><label>Prenom *</label>
<input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>"></div>
<div class="champ"><label>Login *</label>
<input type="text" name="login" value="<?= htmlspecialchars($login) ?>"></div>
<div class="champ">
<label>Nouveau mot de passe (vide = inchange)</label>
<input type="password" name="mot_de_passe">
</div>
<div class="champ"><label>Role</label>
<select name="role">
<option value="editeur" <?= $role==='editeur'?'selected':'' ?>>Editeur</option>
<option value="administrateur" <?= $role==='administrateur'?'selected':''
?>>Administrateur</option>
</select></div>
<button type="submit" class="btn-primaire">Enregistrer</button>
<a href="liste.php" class="btn-secondaire">Annuler</a>
</form>
</main>
<?php include __DIR__ . '/../pied.php'; ?>
</body></html>
