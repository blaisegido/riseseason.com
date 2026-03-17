<?php
$dbConfig = require __DIR__ . '/config/database.php';
$db = $dbConfig();
$stmt = $db->query("SHOW COLUMNS FROM transactions");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
