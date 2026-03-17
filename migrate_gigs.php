<?php
$db = new PDO("mysql:host=localhost;port=3306;dbname=riseseason;charset=utf8mb4", 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $db->exec("ALTER TABLE gigs CHANGE status status enum('pending','approved','rejected','paused') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending'");
    echo "Enum updated.\n";
} catch (Exception $e) {
    echo "Enum error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE gigs ADD COLUMN deleted_at timestamp NULL DEFAULT NULL");
    echo "deleted_at column added.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
         echo "deleted_at already exists.\n";
    } else {
        echo "Column error: " . $e->getMessage() . "\n";
    }
}
