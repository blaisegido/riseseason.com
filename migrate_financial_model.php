<?php
$dbConfig = require __DIR__ . '/config/database.php';
$db = $dbConfig();

try {
    echo "--- Financial Model Migration ---\n";

    // 1. Update users table
    echo "Updating 'users' table...\n";
    $userColumns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    
    $userUpdates = [
        'subscription_status' => "ENUM('free', 'premium') DEFAULT 'free' AFTER role",
        'subscription_expires_at' => "DATETIME NULL AFTER subscription_status",
        'rise_seeds' => "INT DEFAULT 0 AFTER subscription_expires_at",
        'performance_score' => "DECIMAL(5,2) DEFAULT 0.00 AFTER rise_seeds"
    ];

    foreach ($userUpdates as $col => $def) {
        if (!in_array($col, $userColumns)) {
            $db->exec("ALTER TABLE users ADD COLUMN $col $def");
            echo "- Column '$col' added to 'users'.\n";
        }
    }

    // 2. Update gigs table
    echo "Updating 'gigs' table...\n";
    $gigColumns = $db->query("SHOW COLUMNS FROM gigs")->fetchAll(PDO::FETCH_COLUMN);
    
    $gigUpdates = [
        'is_sponsored' => "BOOLEAN DEFAULT 0 AFTER status",
        'sponsorship_expires_at' => "DATETIME NULL AFTER is_sponsored"
    ];

    foreach ($gigUpdates as $col => $def) {
        if (!in_array($col, $gigColumns)) {
            $db->exec("ALTER TABLE gigs ADD COLUMN $col $def");
            echo "- Column '$col' added to 'gigs'.\n";
        }
    }

    // 3. Create transactions table
    echo "Creating 'transactions' table...\n";
    $db->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type ENUM('subscription', 'seeds_purchase', 'sponsorship_spend') NOT NULL,
        amount DECIMAL(10,2) DEFAULT 0.00,
        seeds INT DEFAULT 0,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "- Table 'transactions' ready.\n";

    echo "\nMigration completed successfully.\n";
} catch (Exception $e) {
    die("\nERROR: " . $e->getMessage() . "\n");
}
