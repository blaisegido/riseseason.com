<?php
$dbConfig = require __DIR__ . '/config/database.php';
$db = $dbConfig();

try {
    echo "--- Renaming RiseSeeds to Seeds ---\n";

    // Renaming 'rise_seeds' to 'seeds' in 'users' table
    $columns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);

    if (in_array('rise_seeds', $columns)) {
        $db->exec("ALTER TABLE users CHANGE COLUMN rise_seeds seeds INT DEFAULT 0");
        echo "- Column 'rise_seeds' renamed to 'seeds' in 'users' table.\n";
    } else {
        echo "- Column 'rise_seeds' not found (maybe already renamed).\n";
    }

    echo "\nMigration completed.\n";
} catch (Exception $e) {
    die("\nERROR: " . $e->getMessage() . "\n");
}
