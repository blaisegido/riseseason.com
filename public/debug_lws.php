<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Diagnostic RiseSeason - LWS</h1>";

// 1. Check Document Root
echo "<h2>1. Environnement</h2>";
echo "Dossier actuel : " . __DIR__ . "<br>";
echo "Script : " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "PHP Version : " . phpversion() . "<br>";

// 2. Try DB Connection
echo "<h2>2. Test Base de Données</h2>";
$db_file = __DIR__ . '/../config/database.php';
if (file_exists($db_file)) {
    echo "Fichier de configuration trouvé.<br>";
    $pdoFactory = require $db_file;
    try {
        $pdo = $pdoFactory();
        echo "<b style='color:green'>Connexion réussie !</b>";
    } catch (\Exception $e) {
        echo "<b style='color:red'>Erreur de connexion :</b> " . $e->getMessage();
    }
} else {
    echo "<b style='color:red'>Erreur :</b> Fichier config/database.php introuvable.";
}

// 3. Check Folders
echo "<h2>3. Vérification des dossiers</h2>";
$folders = ['../views', '../src', '../vendor'];
foreach ($folders as $f) {
    if (is_dir($f)) {
        echo "[OK] Dossier $f existe.<br>";
    } else {
        echo "[ERREUR] Dossier $f introuvable.<br>";
    }
}
