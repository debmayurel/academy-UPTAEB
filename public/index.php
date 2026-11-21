<?php

declare(strict_types=1);

// Allow PHP's built-in server to serve static files directly (CSS, JS, images)
if (PHP_SAPI === 'cli-server') {
    $requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $staticFile = __DIR__ . $requestedPath;
    if ($requestedPath !== '/' && is_file($staticFile)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

use App\Config\DatabaseConnectionFactory;
use App\Controllers\AuthController;
use App\Controllers\PasswordResetController;
use App\Controllers\UserController;
use App\Core\Router;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;
use App\Services\DbAuthenticator;
use App\Services\PasswordResetService;
use App\Services\PhpSessionManager;
use App\Services\UserService;
use App\Validation\LoginInputValidator;
use App\Validation\PasswordResetValidator;
use App\Validation\UserValidator;

$pdo = DatabaseConnectionFactory::create();

// Repositories
$userRepository = new UserRepository($pdo);

// Validators
$loginValidator = new LoginInputValidator();
$userValidator = new UserValidator();
$passwordResetValidator = new PasswordResetValidator();

// Services
$authenticator = new DbAuthenticator($userRepository);
$sessionManager = new PhpSessionManager();
$authService = new AuthenticationService($authenticator, $sessionManager);
$userService = new UserService($userRepository, $userValidator);
$passwordResetService = new PasswordResetService(
    $userRepository,
    $passwordResetValidator
);

// Controllers (MVC)
$authController = new AuthController($authService, $loginValidator, $userService);
$passwordResetController = new PasswordResetController($authService, $passwordResetService);
$userController = new UserController($authService, $userService);

// Routes
$router = new Router();

// Views / Pages
$router->get('/', [$userController, 'dashboard']);
$router->get('/login', [$authController, 'showLogin']);
$router->post('/login', [$authController, 'login']);
$router->get('/register', [$authController, 'showRegister']);
$router->post('/register', [$authController, 'register']);
$router->get('/forgot-password', [$passwordResetController, 'showForgotPassword']);
$router->get('/logout', [$authController, 'logout']);

// AJAX JSON API
$router->post('/api/login', [$authController, 'login']);
$router->post('/api/register', [$authController, 'register']);
$router->post('/api/logout', [$authController, 'logout']);
$router->post('/api/reset-password', [$passwordResetController, 'resetPassword']);

// User CRUD API
$router->get('/api/users', [$userController, 'index']);
$router->get('/api/users/{id}', [$userController, 'show']);
$router->post('/api/users', [$userController, 'store']);
$router->put('/api/users/{id}', [$userController, 'update']);
$router->delete('/api/users/{id}', [$userController, 'destroy']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
