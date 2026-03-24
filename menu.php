<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// On définit le chemin racine du projet pour éviter les erreurs d'inclusion
$root = $_SERVER['DOCUMENT_ROOT'] . '/Site_Actu_Dynamique';

// Inclusion sécurisée de session.php et db.php (pour le compteur)
if (file_exists($root . '/config/session.php')) {
    require_once $root . '/config/session.php';
}
if (file_exists($root . '/config/db.php')) {
    require_once $root . '/config/db.php';
}

$role = $_SESSION['utilisateur']['role'] ?? 'visiteur';

// --- OPTIONNEL : COMPTEUR DE LA CORBEILLE ---
$nb_archives = 0;
if ($role === 'editeur' || $role === 'administrateur') {
    $pdo_menu = getPDO();
    $nb_archives = $pdo_menu->query("SELECT COUNT(*) FROM articles WHERE est_supprime = 1")->fetchColumn();
}
?>
<nav class="menu">
    <div class="container">
        <ul>
            <li><a href="/Site_Actu_Dynamique/accueil.php">Accueil</a></li>
            
            <?php if ($role === 'editeur' || $role === 'administrateur'): ?>
                <li><a href="/Site_Actu_Dynamique/articles/liste.php">Mes articles</a></li>
                <li><a href="/Site_Actu_Dynamique/articles/ajouter.php">+ Article</a></li>
                <li><a href="/Site_Actu_Dynamique/categories/liste.php">Catégories</a></li>
                
                <li>
                    <a href="/Site_Actu_Dynamique/articles/corbeille.php" style="color: #e94560;">
                        🗑️ Corbeille 
                        <?php if ($nb_archives > 0): ?>
                            <span style="background:#e94560; color:#fff; padding:2px 6px; border-radius:10px; font-size:0.7rem;">
                                <?= $nb_archives ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'administrateur'): ?>
                <li><a href="/Site_Actu_Dynamique/utilisateurs/liste.php">Utilisateurs</a></li>
            <?php endif; ?>
        </ul>

        <div class="menu-droite">
            <?php if (isset($_SESSION['utilisateur'])): ?>
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