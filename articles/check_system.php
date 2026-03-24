<?php
$dossier = __DIR__ . '/uploads';

echo "<h1>Diagnostic du Système - Site Actu Dynamique</h1>";

// 1. Vérification du dossier
if (!is_dir($dossier)) {
    echo "<p style='color:red;'>Le dossier 'uploads' n'existe pas.</p>";
} else {
    echo "<p style='color:green;'>Le dossier 'uploads' est présent.</p>";
    
    // 2. Vérification des droits d'écriture
    if (is_writable($dossier)) {
        echo "<p style='color:green;'>Le dossier est accessible en écriture (Prêt pour l'upload).</p>";
    } else {
        echo "<p style='color:red;'>Erreur : PHP ne peut pas écrire dans le dossier. Vérifiez les permissions.</p>";
    }
}

// 3. Vérification des limites PHP (pour ne pas être bloqué par des images lourdes)
echo "<h3>Configuration PHP :</h3>";
echo "<ul>";
echo "<li>Taille max fichier (upload_max_filesize) : " . ini_get('upload_max_filesize') . "</li>";
echo "<li>Mémoire limite (memory_limit) : " . ini_get('memory_limit') . "</li>";
echo "</ul>";

if (strpos(ini_get('upload_max_filesize'), 'M') !== false && (int)ini_get('upload_max_filesize') < 2) {
    echo "<p style='color:orange;'>Attention : La limite d'upload est basse. Évitez les photos de plus de 2Mo.</p>";
}
?>