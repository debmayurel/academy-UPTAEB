<?php

namespace App\Validation;

use App\Interfaces\LoginValidatorInterface;
use App\Interfaces\ValidationResultInterface;

class LoginInputValidator implements LoginValidatorInterface
{
    public function validate(array $inputData): ValidationResultInterface
    {
        $errors = [];
        $email = trim((string) ($inputData['email'] ?? ''));
        $password = (string) ($inputData['password'] ?? '');

        if ($email === '') {
            $errors['email'] = 'El correo es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El formato del correo no es válido.';
        }

        if (trim($password) === '') {
            $errors['password'] = 'La contraseña es obligatoria.';
        }

        return ValidationResult::fromErrors($errors);
    }
}
