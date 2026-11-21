<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\UserService;

class UserController extends BaseController
{
    public function __construct(
        private AuthenticationService $authService,
        private UserService $userService
    ) {
    }

    public function dashboard(): void
    {
        if (!$this->authService->isAuthenticated()) {
            $this->redirect('/login');
        }

        $currentUserEmail = $this->authService->getAuthenticatedUser();
        $this->render('dashboard', [
            'currentUserEmail' => $currentUserEmail,
        ]);
    }

    public function index(): void
    {
        if (!$this->ensureApiAuthenticated()) {
            return;
        }

        $search = isset($_GET['search']) ? (string) $_GET['search'] : null;
        $users = $this->userService->listUsers($search);

        $this->json([
            'ok' => true,
            'users' => array_map(static fn(User $user): array => $user->toArray(), $users),
        ]);
    }

    public function show(string $id): void
    {
        if (!$this->ensureApiAuthenticated()) {
            return;
        }

        if (!ctype_digit($id)) {
            $this->json(['ok' => false, 'message' => 'ID de usuario inválido.'], 400);
            return;
        }

        $user = $this->userService->getUser((int) $id);
        if ($user === null) {
            $this->json(['ok' => false, 'message' => 'Usuario no encontrado.'], 404);
            return;
        }

        $this->json([
            'ok' => true,
            'user' => $user->toArray(),
        ]);
    }

    public function store(): void
    {
        if (!$this->ensureApiAuthenticated()) {
            return;
        }

        $result = $this->userService->createUser($this->getRequestData());
        $status = $result['status'];
        unset($result['status']);

        $this->json($result, $status);
    }

    public function update(string $id): void
    {
        if (!$this->ensureApiAuthenticated()) {
            return;
        }

        if (!ctype_digit($id)) {
            $this->json(['ok' => false, 'message' => 'ID de usuario inválido.'], 400);
            return;
        }

        $userId = (int) $id;
        $beforeUpdate = $this->userService->getUser($userId);
        $currentEmail = $this->authService->getAuthenticatedUser();

        $result = $this->userService->updateUser($userId, $this->getRequestData());
        $status = $result['status'];
        unset($result['status']);

        if (
            $result['ok']
            && $beforeUpdate !== null
            && $currentEmail !== null
            && strtolower($beforeUpdate->getEmail()) === strtolower($currentEmail)
            && isset($result['user']['email'])
        ) {
            $this->authService->refreshAuthenticatedUser((string) $result['user']['email']);
        }

        $this->json($result, $status);
    }

    public function destroy(string $id): void
    {
        if (!$this->ensureApiAuthenticated()) {
            return;
        }

        if (!ctype_digit($id)) {
            $this->json(['ok' => false, 'message' => 'ID de usuario inválido.'], 400);
            return;
        }

        $result = $this->userService->deleteUser(
            (int) $id,
            $this->authService->getAuthenticatedUser()
        );
        $status = $result['status'];
        unset($result['status']);

        $this->json($result, $status);
    }

    private function ensureApiAuthenticated(): bool
    {
        if (!$this->authService->isAuthenticated()) {
            $this->json([
                'ok' => false,
                'message' => 'No autorizado. Inicia sesión para continuar.',
            ], 401);
            return false;
        }

        return true;
    }
}
