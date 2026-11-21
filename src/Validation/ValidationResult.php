<?php

namespace App\Validation;

use App\Interfaces\ValidationResultInterface;

class ValidationResult implements ValidationResultInterface
{
    private bool $isValid;
    private string $errorMessage;
    /** @var array<string, string> */
    private array $errors;

    /**
     * @param array<string, string> $errors
     */
    private function __construct(bool $isValid, string $errorMessage = '', array $errors = [])
    {
        $this->isValid = $isValid;
        $this->errorMessage = $errorMessage;
        $this->errors = $errors;
    }

    public static function success(): self
    {
        return new self(true);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function failure(string $errorMessage, array $errors = []): self
    {
        return new self(false, $errorMessage, $errors);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function fromErrors(array $errors): self
    {
        if ($errors === []) {
            return self::success();
        }

        $firstMessage = reset($errors);

        return new self(false, is_string($firstMessage) ? $firstMessage : 'Error de validación.', $errors);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
