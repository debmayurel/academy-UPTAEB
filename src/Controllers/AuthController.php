<?php

namespace App\Controllers;

use App\Interfaces\LoginValidatorInterface;
use App\Services\AuthenticationService;
use App\Services\UserService;

class AuthController extends BaseController
{
    public function __construct(
        private AuthenticationService $authService,
        private LoginValidatorInterface $loginValidator,
        private UserService $userService
    ) {
    }

    public function showLogin(string $error = ''): void
    {
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('login', ['error' => $error]);
    }

    public function login(): void
    {
        $data = $this->getRequestData();
        $validation = $this->loginValidator->validate($data);

        if (!$validation->isValid()) {
            if ($this->isAjax()) {
                $this->json([
                    'ok' => false,
                    'message' => $validation->getErrorMessage(),
                    'errors' => $validation->getErrors(),
                ], 422);
                return;
            }

            $this->showLogin($validation->getErrorMessage());
            return;
        }

        $email = (string) ($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if (!$this->authService->attempt($email, $password)) {
            $message = 'Credenciales incorrectas.';
            if ($this->isAjax()) {
                $this->json([
                    'ok' => false,
                    'message' => $message,
                    'errors' => ['password' => $message],
                ], 401);
                return;
            }

            $this->showLogin($message);
            return;
        }

        if ($this->isAjax()) {
            $this->json([
                'ok' => true,
                'message' => 'Inicio de sesión exitoso.',
                'redirect' => '/',
            ]);
            return;
        }

        $this->redirect('/');
    }

    public function showRegister(string $error = ''): void
    {
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('register', ['error' => $error]);
    }

    public function register(): void
    {
        $data = $this->getRequestData();
        $result = $this->userService->createUser($data);

        if ($this->isAjax()) {
            $status = $result['status'];
            unset($result['status']);
            if ($result['ok']) {
                $result['redirect'] = '/login';
            }
            $this->json($result, $status);
            return;
        }

        if (!$result['ok']) {
            $this->showRegister($result['message']);
            return;
        }

        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->authService->logout();

        if ($this->isAjax()) {
            $this->json([
                'ok' => true,
                'message' => 'Sesión cerrada.',
                'redirect' => '/login',
            ]);
            return;
        }

        $this->redirect('/login');
    }
}
