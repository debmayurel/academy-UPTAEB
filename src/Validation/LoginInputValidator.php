<?php

namespace App\Validation;

use App\Interfaces\LoginValidatorInterface;
use App\Interfaces\ValidationResultInterface;

class LoginInputValidator implements LoginValidatorInterface
{
    public function validate(array $inputData): ValidationResultInterface
    {
        $email = trim((string)($inputData['email'] ?? ''));
        $password = trim((string)($inputData['password'] ?? ''));

        if ($this->hasEmptyFields($email, $password)) {
            return ValidationResult::failure('Debes llenar todos los campos.');
        }

        if ($this->isInvalidEmailFormat($email)) {
            return ValidationResult::failure('El correo no es válido.');
        }

        return ValidationResult::success();
    }

    private function hasEmptyFields(string $email, string $password): bool
    {
        return $email === '' || $password === '';
    }

    private function isInvalidEmailFormat(string $email): bool
    {
        return !filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
