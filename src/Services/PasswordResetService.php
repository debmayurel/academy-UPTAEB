<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Validation\PasswordResetValidator;

class PasswordResetService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetValidator $validator
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function resetPassword(array $inputData): array
    {
        $validation = $this->validator->validateReset($inputData);
        if (!$validation->isValid()) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => $validation->getErrorMessage(),
                'errors' => $validation->getErrors(),
            ];
        }

        $email = strtolower(trim((string) $inputData['email']));
        $newPassword = (string) $inputData['password'];

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            return [
                'ok' => false,
                'status' => 404,
                'message' => 'No existe ninguna cuenta registrada con ese correo.',
                'errors' => ['email' => 'No existe ninguna cuenta registrada con ese correo.'],
            ];
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userRepository->updatePasswordByEmail($email, $passwordHash);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.',
        ];
    }
}
