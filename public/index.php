<?php
declare(strict_types=1);


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

session_start();

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('check_csrf')) {
    function check_csrf(?string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
    }
}

$pdoFactory = require __DIR__ . '/../config/database.php';
\Flight::set('db', $pdoFactory());

// Global activity tracking
if (!empty($_SESSION['user_id'])) {
    \App\Models\User::updateLastSeen((int)$_SESSION['user_id']);
}

\Flight::map('renderView', function (string $view, array $params = [], string $layout = 'main'): void {
    extract($params, EXTR_SKIP);
    $viewPath = __DIR__ . '/../views/' . $view . '.php';
    $layoutPath = __DIR__ . '/../views/layouts/' . $layout . '.php';

    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    require $layoutPath;
});

require __DIR__ . '/../src/routes.php';

\Flight::start();