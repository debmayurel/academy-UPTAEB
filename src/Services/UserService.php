<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Validation\UserValidator;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserValidator $validator
    ) {
    }

    /**
     * @return User[]
     */
    public function listUsers(?string $search = null): array
    {
        return $this->userRepository->findAll($search);
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @return array{ok: bool, status: int, message: string, errors?: array<string, string>, user?: array<string, int|string>}
     */
    public function createUser(array $inputData): array
    {
        $validation = $this->validator->validateCreate($inputData);
        if (!$validation->isValid()) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => $validation->getErrorMessage(),
                'errors' => $validation->getErrors(),
            ];
        }

        $name = trim((string) $inputData['name']);
        $email = strtolower(trim((string) $inputData['email']));
        $password = (string) $inputData['password'];

        if ($this->userRepository->findByEmail($email) !== null) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Ya existe un usuario registrado con ese correo.',
                'errors' => ['email' => 'Ya existe un usuario registrado con ese correo.'],
            ];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userRepository->create($name, $email, $passwordHash);

        return [
            'ok' => true,
            'status' => 201,
            'message' => 'Usuario creado correctamente.',
            'user' => $user->toArray(),
        ];
    }

    /**
     * @return array{ok: bool, status: int, message: string, errors?: array<string, string>, user?: array<string, int|string>}
     */
    public function updateUser(int $id, array $inputData): array
    {
        $existing = $this->userRepository->findById($id);
        if ($existing === null) {
            return [
                'ok' => false,
                'status' => 404,
                'message' => 'Usuario no encontrado.',
            ];
        }

        $validation = $this->validator->validateUpdate($inputData);
        if (!$validation->isValid()) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => $validation->getErrorMessage(),
                'errors' => $validation->getErrors(),
            ];
        }

        $name = trim((string) $inputData['name']);
        $email = strtolower(trim((string) $inputData['email']));
        $password = (string) ($inputData['password'] ?? '');

        $ownerWithEmail = $this->userRepository->findByEmail($email);
        if ($ownerWithEmail !== null && $ownerWithEmail->getId() !== $id) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Otro usuario ya está usando ese correo.',
                'errors' => ['email' => 'Otro usuario ya está usando ese correo.'],
            ];
        }

        $passwordHash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;
        $updated = $this->userRepository->update($id, $name, $email, $passwordHash);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Usuario actualizado correctamente.',
            'user' => $updated?->toArray() ?? $existing->toArray(),
        ];
    }

    /**
     * @return array{ok: bool, status: int, message: string}
     */
    public function deleteUser(int $id, ?string $currentUserEmail = null): array
    {
        $existing = $this->userRepository->findById($id);
        if ($existing === null) {
            return [
                'ok' => false,
                'status' => 404,
                'message' => 'Usuario no encontrado.',
            ];
        }

        if ($currentUserEmail !== null && strtolower($existing->getEmail()) === strtolower($currentUserEmail)) {
            return [
                'ok' => false,
                'status' => 409,
                'message' => 'No puedes eliminar tu propia cuenta mientras tienes la sesión activa.',
            ];
        }

        $this->userRepository->delete($id);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Usuario eliminado correctamente.',
        ];
    }
}
