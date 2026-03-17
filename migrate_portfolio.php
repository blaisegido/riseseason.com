<?php
$db = new PDO("mysql:host=localhost;port=3306;dbname=riseseason;charset=utf8mb4", 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $columns = $db->query("SHOW COLUMNS FROM portfolio_files")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('title', $columns)) {
        $db->exec("ALTER TABLE portfolio_files ADD COLUMN title VARCHAR(120) NULL AFTER user_id");
        echo "Added column 'title'.\n";
    }
    
    if (!in_array('description', $columns)) {
        $db->exec("ALTER TABLE portfolio_files ADD COLUMN description TEXT NULL AFTER title");
        echo "Added column 'description'.\n";
    }
    
    echo "Portfolio table update check completed.\n";
} catch (Exception $e) {
    echo "Error updating portfolio table: " . $e->getMessage() . "\n";
}
