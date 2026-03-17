<?php
$db = new PDO("mysql:host=localhost;port=3306;dbname=riseseason;charset=utf8mb4", 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            gig_id INT UNSIGNED NOT NULL,
            order_id INT UNSIGNED NOT NULL UNIQUE,
            buyer_id INT UNSIGNED NOT NULL,
            freelancer_id INT UNSIGNED NOT NULL,
            rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_reviews_freelancer (freelancer_id),
            INDEX idx_reviews_gig (gig_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Table 'reviews' created successfully.\n";
} catch (Exception $e) {
    echo "Error creating table 'reviews': " . $e->getMessage() . "\n";
}
