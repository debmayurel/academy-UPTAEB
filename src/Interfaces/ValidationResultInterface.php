<?php

namespace App\Interfaces;

interface ValidationResultInterface
{
    public function isValid(): bool;

    public function getErrorMessage(): string;

    /**
     * @return array<string, string>
     */
    public function getErrors(): array;
}
