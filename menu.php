<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/session.php';
$role = $_SESSION['utilisateur']['role'] ?? 'visiteur';
?>
<nav class="menu">
    <div class="container">
        <ul>
            <li><a href="/Site_Actu_Dynamique/accueil.php">Accueil</a></li>
            <?php if ($role === 'editeur' || $role === 'administrateur'): ?>
                <li><a href="/Site_Actu_Dynamique/articles/liste.php">Mes articles</a></li>
                <li><a href="/Site_Actu_Dynamique/articles/ajouter.php">+ Article</a></li>
                <li><a href="/Site_Actu_Dynamique/categories/liste.php">Catégories</a></li>
            <?php endif; ?>
            <?php if ($role === 'administrateur'): ?>
                <li><a href="/Site_Actu_Dynamique/utilisateurs/liste.php">Utilisateurs</a></li>
            <?php endif; ?>
        </ul>
        <div class="menu-droite">
            <?php if (estConnecte()): ?>
                <span class="bienvenue">
                    👤 <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?>
                    <em>(<?= htmlspecialchars($role) ?>)</em>
                </span>
                <a href="/Site_Actu_Dynamique/deconnexion.php" class="btn-deconnexion">Déconnexion</a>
            <?php else: ?>
                <a href="/Site_Actu_Dynamique/connexion.php" class="btn-connexion">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>