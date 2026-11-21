<?php

namespace App\Services;

use App\Interfaces\SessionManagerInterface;

class PhpSessionManager implements SessionManagerInterface
{
    public function set(string $key, mixed $value): void
    {
        $this->ensureSessionActive();
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        $this->ensureSessionActive();
        return $_SESSION[$key] ?? null;
    }

    public function has(string $key): bool
    {
        $this->ensureSessionActive();
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        $this->ensureSessionActive();
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        $this->ensureSessionActive();
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    private function ensureSessionActive(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
