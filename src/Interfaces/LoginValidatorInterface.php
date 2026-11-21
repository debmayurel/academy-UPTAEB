<?php

namespace App\Interfaces;

interface LoginValidatorInterface
{
    public function validate(array $inputData): ValidationResultInterface;
}
