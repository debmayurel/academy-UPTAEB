<?php

namespace App\Controllers;

use App\Interfaces\LoginValidatorInterface;
use App\Services\AuthenticationService;

function showLoginForm(AuthenticationService $authService, string $errorMessage = ''): void
{
    if ($authService->isAuthenticated()) {
        redirect('/');
        return;
    }

    $error = $errorMessage;
    include __DIR__ . '/../views/login.php';
}

function processLogin(
    array $postData,
    AuthenticationService $authService,
    LoginValidatorInterface $validator
): void {
    $validationResult = $validator->validate($postData);
    if (!$validationResult->isValid()) {
        showLoginForm($authService, $validationResult->getErrorMessage());
        return;
    }

    $email = (string) ($postData['email'] ?? '');
    $password = (string) ($postData['password'] ?? '');

    if (!$authService->attempt($email, $password)) {
        showLoginForm($authService, 'Credenciales incorrectas.');
        return;
    }

    redirect('/');
}

function logout(AuthenticationService $authService): void
{
    $authService->logout();
    redirect('/login');
}

function showDashboard(AuthenticationService $authService): void
{
    if (!$authService->isAuthenticated()) {
        redirect('/login');
        return;
    }

    $currentUserEmail = $authService->getAuthenticatedUser();
    include __DIR__ . '/../views/dashboard.php';
}

function showNotFound(): void
{
    http_response_code(404);
    echo '<h1>404 Not Found</h1>';
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}
