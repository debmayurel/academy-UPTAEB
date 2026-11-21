<?php

namespace App\Services;

use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\UserRepositoryInterface;

class DbAuthenticator implements AuthenticatorInterface
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function authenticate(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail(strtolower(trim($email)));

        return $user !== null && password_verify($password, $user->getPasswordHash());
    }
}
