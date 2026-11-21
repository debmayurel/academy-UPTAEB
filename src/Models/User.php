<?php

namespace App\Models;

class User
{
    public function __construct(
        private int $id,
        private string $email,
        private string $passwordHash,
        private string $createdAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
