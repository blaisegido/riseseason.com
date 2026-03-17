<?php
$db = new PDO("mysql:host=localhost;port=3306;dbname=riseseason;charset=utf8mb4", 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    // Check if columns already exist to avoid errors
    $columns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    
    $fields = [
        'title' => "VARCHAR(120) NULL AFTER role",
        'daily_rate' => "DECIMAL(10,2) NULL AFTER title",
        'languages' => "VARCHAR(255) NULL AFTER daily_rate",
        'availability' => "ENUM('available','soon','unavailable') DEFAULT 'available' AFTER languages",
        'website' => "VARCHAR(255) NULL AFTER availability",
        'linkedin' => "VARCHAR(255) NULL AFTER website",
        'github' => "VARCHAR(255) NULL AFTER linkedin"
    ];

    foreach ($fields as $field => $definition) {
        if (!in_array($field, $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN $field $definition");
            echo "Added column '$field'.\n";
        } else {
            echo "Column '$field' already exists.\n";
        }
    }
    
    echo "Table 'users' update check completed.\n";
} catch (Exception $e) {
    echo "Error updating table 'users': " . $e->getMessage() . "\n";
}
