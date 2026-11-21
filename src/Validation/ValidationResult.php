<?php

namespace App\Validation;

use App\Interfaces\ValidationResultInterface;

class ValidationResult implements ValidationResultInterface
{
    private bool $isValid;
    private string $errorMessage;

    private function __construct(bool $isValid, string $errorMessage = '')
    {
        $this->isValid = $isValid;
        $this->errorMessage = $errorMessage;
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function failure(string $errorMessage): self
    {
        return new self(false, $errorMessage);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
