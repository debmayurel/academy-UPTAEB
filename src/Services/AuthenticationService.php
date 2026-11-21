<?php

namespace App\Services;

use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\SessionManagerInterface;

class AuthenticationService
{
    private const SESSION_USER_KEY = 'user_email';

    private AuthenticatorInterface $authenticator;
    private SessionManagerInterface $sessionManager;

    public function __construct(
        AuthenticatorInterface $authenticator,
        SessionManagerInterface $sessionManager
    ) {
        $this->authenticator = $authenticator;
        $this->sessionManager = $sessionManager;
    }

    public function attempt(string $email, string $password): bool
    {
        $normalizedEmail = strtolower(trim($email));

        if (!$this->authenticator->authenticate($normalizedEmail, $password)) {
            return false;
        }

        $this->sessionManager->set(self::SESSION_USER_KEY, $normalizedEmail);
        return true;
    }

    public function logout(): void
    {
        $this->sessionManager->remove(self::SESSION_USER_KEY);
        $this->sessionManager->destroy();
    }

    public function isAuthenticated(): bool
    {
        return $this->sessionManager->has(self::SESSION_USER_KEY);
    }

    public function getAuthenticatedUser(): ?string
    {
        return $this->sessionManager->get(self::SESSION_USER_KEY);
    }

    public function refreshAuthenticatedUser(string $email): void
    {
        if ($this->isAuthenticated()) {
            $this->sessionManager->set(self::SESSION_USER_KEY, strtolower(trim($email)));
        }
    }
}
