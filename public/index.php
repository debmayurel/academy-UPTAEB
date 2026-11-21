<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config\DatabaseConnectionFactory;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;
use App\Services\DbAuthenticator;
use App\Services\PhpSessionManager;
use App\Validation\LoginInputValidator;

require_once __DIR__ . '/../src/Controllers/auth_functions.php';

$pdo = DatabaseConnectionFactory::create();
$userRepository = new UserRepository($pdo);
$authenticator = new DbAuthenticator($userRepository);
$sessionManager = new PhpSessionManager();
$authService = new AuthenticationService($authenticator, $sessionManager);
$validator = new LoginInputValidator();

$route = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($route === '/logout') {
    App\Controllers\logout($authService);
} elseif ($route === '/login') {
    if ($method === 'POST') {
        App\Controllers\processLogin($_POST, $authService, $validator);
    } else {
        App\Controllers\showLoginForm($authService);
    }
} elseif ($route === '/') {
    App\Controllers\showDashboard($authService);
} else {
    App\Controllers\showNotFound();
}
