<?php
// Utilisation de constantes pour la configuration
define('DB_HOST',    'localhost');
define('DB_NAME',    'site_actualite');
define('DB_USER',    'root');
define('DB_PASS',    ''); // Par défaut vide sur XAMPP
define('DB_CHARSET', 'utf8mb4');

/**
 * Retourne une instance de connexion PDO (Singleton)
 */
function getPDO(): PDO
{
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lance des erreurs si SQL échoue
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne des tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En développement, on affiche l'erreur réelle pour comprendre le souci
            // En production, on utiliserait un message générique
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }
    
    return $pdo;
}