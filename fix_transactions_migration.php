<?php
$dbConfig = require __DIR__ . '/config/database.php';
$db = $dbConfig();

try {
    echo "--- Fixing Transactions Table Schema ---\n";

    $columns = $db->query("SHOW COLUMNS FROM transactions")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('seeds', $columns)) {
        $db->exec("ALTER TABLE transactions ADD COLUMN seeds INT DEFAULT 0 AFTER amount");
        echo "- Column 'seeds' added.\n";
    }

    if (!in_array('status', $columns)) {
        $db->exec("ALTER TABLE transactions ADD COLUMN status ENUM('pending', 'completed', 'failed') DEFAULT 'pending' AFTER seeds");
        echo "- Column 'status' added.\n";
    }

    // Update 'type' enum to include new values if necessary
    // Existing: enum('credit','debit','withdrawal','subscription')
    // New required: 'subscription', 'seeds_purchase', 'sponsorship_spend'
    $db->exec("ALTER TABLE transactions MODIFY COLUMN type ENUM('credit','debit','withdrawal','subscription', 'seeds_purchase', 'sponsorship_spend') NOT NULL");
    echo "- Enum 'type' updated.\n";

    echo "\nMigration fix completed.\n";
} catch (Exception $e) {
    die("\nERROR: " . $e->getMessage() . "\n");
}
