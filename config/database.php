<?php

declare(strict_types=1);

return static function (): \PDO {
    $hosts = ['localhost', '127.0.0.1'];
    $port = 3306;
    $db   = 'riseseason';
    $user = 'root';
    $pass = '';
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