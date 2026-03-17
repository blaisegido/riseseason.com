<?php
$dbConfig = require 'config/database.php';
$pdo = $dbConfig();

try {
    echo "--- Diagnostic Base de Données ---\n";
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Base actuelle : $dbName\n";

    echo "\n--- Tables existantes ---\n";
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table\n";
    }

    if (in_array('users', $tables)) {
        echo "\n--- Structure de la table 'users' ---\n";
        $columns = $pdo->query('DESCRIBE users')->fetchAll();
        foreach ($columns as $col) {
            echo "{$col['Field']} | {$col['Type']} | {$col['Null']} | {$col['Key']} | {$col['Default']} | {$col['Extra']}\n";
        }
        
        echo "\n--- Mode de stockage (Engine) ---\n";
        $status = $pdo->query("SHOW TABLE STATUS LIKE 'users'")->fetch();
        echo "Engine: {$status['Engine']}\n";
        echo "Collation: {$status['Collation']}\n";
    } else {
        echo "\nERREUR : La table 'users' n'existe pas dans la base '$dbName'.\n";
    }

} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage();
}
