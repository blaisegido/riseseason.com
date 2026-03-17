<?php
$db = new PDO("mysql:host=localhost;port=3306;dbname=riseseason;charset=utf8mb4", 'root', '');
$stmt = $db->query("SHOW CREATE TABLE gigs");
print_r($stmt->fetchColumn(1));
