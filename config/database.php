<?php

declare(strict_types=1);

return static function (): \PDO {
    $hosts = [getenv('DB_HOST') ?: 'localhost', '127.0.0.1'];
    $port = (int)(getenv('DB_PORT') ?: 3306);
    $db   = getenv('DB_NAME') ?: 'riseseason';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $charset = 'utf8mb4';

    $lastError = null;

    foreach ($hosts as $host) {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

        try {
            return new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (\PDOException $e) {
            $lastError = $e->getMessage();
        }
    }

    http_response_code(500);
    exit('Erreur connexion base de donnees: ' . (string)$lastError);
};