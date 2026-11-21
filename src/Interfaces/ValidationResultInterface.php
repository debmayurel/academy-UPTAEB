<?php

namespace App\Interfaces;

interface ValidationResultInterface
{
    public function isValid(): bool;

    public function getErrorMessage(): string;
}
