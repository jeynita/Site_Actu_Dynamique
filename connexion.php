<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

if (estConnecte()) {
    header('Location: /Site_Actu_Dynamique/accueil.php');
    exit;
}

$erreur = '';
$login  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login        = trim($_POST['login'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($login) || empty($mot_de_passe)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $pdo  = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE login = :login LIMIT 1');
        $stmt->execute([':login' => $login]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['utilisateur'] = [
                'id'     => $utilisateur['id'],
                'nom'    => $utilisateur['nom'],
                'prenom' => $utilisateur['prenom'],
                'login'  => $utilisateur['login'],
                'role'   => $utilisateur['role'],
            ];
            header('Location: /Site_Actu_Dynamique/accueil.php');
            exit;
        } else {
            $erreur = 'Login ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="/Site_Actu_Dynamique/css/style.css">
</head>
<body>
<?php include __DIR__ . '/entete.php'; ?>
<?php include __DIR__ . '/menu.php'; ?>
<main class="container">
    <div class="form-wrapper">
        <h1>Connexion</h1>
        <?php if ($erreur): ?>
            <div class="alerte erreur"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <form id="formConnexion" method="post" novalidate>
            <div class="champ">
                <label for="login">Login</label>
                <input type="text" id="login" name="login"
                       value="<?= htmlspecialchars($login) ?>"
                       placeholder="Votre identifiant">
                <span class="erreur-js" id="erreur-login"></span>
            </div>
            <div class="champ">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       placeholder="Votre mot de passe">
                <span class="erreur-js" id="erreur-mdp"></span>
            </div>
            <button type="submit" class="btn-primaire btn-full">Se connecter</button>
        </form>
    </div>
</main>
<?php include __DIR__ . '/pied.php'; ?>
<script>
document.getElementById('formConnexion').addEventListener('submit', function(e) {
    let valide = true;
    document.getElementById('erreur-login').textContent = '';
    document.getElementById('erreur-mdp').textContent   = '';
    if (!document.getElementById('login').value.trim()) {
        document.getElementById('erreur-login').textContent = 'Le login est obligatoire.';
        valide = false;
    }
    if (!document.getElementById('mot_de_passe').value) {
        document.getElementById('erreur-mdp').textContent = 'Le mot de passe est obligatoire.';
        valide = false;
    }
    if (!valide) e.preventDefault();
});
</script>
</body>
</html>