<?php

namespace App\Validation;

use App\Interfaces\ValidationResultInterface;

class PasswordResetValidator
{
    public function validateReset(array $inputData): ValidationResultInterface
    {
        $errors = [];
        $email = trim((string) ($inputData['email'] ?? ''));
        $password = (string) ($inputData['password'] ?? '');
        $passwordConfirmation = (string) ($inputData['password_confirmation'] ?? '');

        if ($email === '') {
            $errors['email'] = 'El correo es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ingresa un correo electrónico válido.';
        }

        if ($password === '') {
            $errors['password'] = 'La nueva contraseña es obligatoria.';
        } elseif (mb_strlen($password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            $errors['password'] = 'La contraseña debe incluir al menos una letra y un número.';
        }

        if ($passwordConfirmation === '') {
            $errors['password_confirmation'] = 'Debes confirmar la nueva contraseña.';
        } elseif ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
        }

        return ValidationResult::fromErrors($errors);
    }
}
