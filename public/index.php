<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load .env file
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load config
$config = require_once __DIR__ . '/../config/config.php';

// Start session with lifetime from config
App\core\Session::start($config['session_lifetime']);

// Load routes
$router = require_once __DIR__ . '/../config/routes.php';

echo $router->resolve();