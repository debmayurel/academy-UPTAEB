<?php

namespace App\Validation;

use App\Interfaces\ValidationResultInterface;

class UserValidator
{
    public function validateCreate(array $inputData): ValidationResultInterface
    {
        return $this->validate($inputData, true);
    }

    public function validateUpdate(array $inputData): ValidationResultInterface
    {
        return $this->validate($inputData, false);
    }

    private function validate(array $inputData, bool $passwordRequired): ValidationResultInterface
    {
        $errors = [];

        $name = trim((string) ($inputData['name'] ?? ''));
        $email = trim((string) ($inputData['email'] ?? ''));
        $password = (string) ($inputData['password'] ?? '');
        $passwordConfirmation = (string) ($inputData['password_confirmation'] ?? '');

        if ($name === '') {
            $errors['name'] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            $errors['name'] = 'El nombre debe tener entre 2 y 100 caracteres.';
        } elseif (!preg_match('/^[\p{L}\s\.\'-]+$/u', $name)) {
            $errors['name'] = 'El nombre solo puede contener letras y espacios.';
        }

        if ($email === '') {
            $errors['email'] = 'El correo es obligatorio.';
        } elseif (mb_strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ingresa un correo electrónico válido.';
        }

        if ($passwordRequired || $password !== '' || $passwordConfirmation !== '') {
            if ($password === '') {
                $errors['password'] = 'La contraseña es obligatoria.';
            } elseif (mb_strlen($password) < 6) {
                $errors['password'] = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
                $errors['password'] = 'La contraseña debe incluir al menos una letra y un número.';
            }

            if ($passwordConfirmation === '') {
                $errors['password_confirmation'] = 'Debes confirmar la contraseña.';
            } elseif ($password !== $passwordConfirmation) {
                $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
            }
        }

        return ValidationResult::fromErrors($errors);
    }
}
