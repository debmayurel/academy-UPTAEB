<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findAll(?string $search = null): array;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(string $name, string $email, string $passwordHash): User;

    public function update(int $id, string $name, string $email, ?string $passwordHash = null): ?User;

    public function updatePasswordByEmail(string $email, string $passwordHash): bool;

    public function delete(int $id): bool;
}
