<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, email, password_hash, created_at FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $userData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($userData === false) {
            return null;
        }

        return new User(
            (int) $userData['id'],
            (string) $userData['email'],
            (string) $userData['password_hash'],
            (string) $userData['created_at']
        );
    }
}
