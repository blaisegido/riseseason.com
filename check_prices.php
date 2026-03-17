<?php
$db = new PDO("mysql:host=localhost;port=3306;dbname=riseseason;charset=utf8mb4", 'root', '');
$stmt = $db->query("SELECT id, title, price_base, price_eur, status FROM gigs LIMIT 10");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
