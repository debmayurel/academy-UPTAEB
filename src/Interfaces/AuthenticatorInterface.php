<?php

namespace App\Interfaces;

interface AuthenticatorInterface
{
    public function authenticate(string $email, string $password): bool;
}
