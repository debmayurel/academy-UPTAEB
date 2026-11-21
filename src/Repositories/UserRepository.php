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

    /**
     * @return User[]
     */
    public function findAll(?string $search = null): array
    {
        $search = trim((string) $search);

        if ($search !== '') {
            $statement = $this->pdo->prepare(
                'SELECT id, name, email, password_hash, created_at
                 FROM users
                 WHERE LOWER(name) LIKE :search OR LOWER(email) LIKE :search
                 ORDER BY id DESC'
            );
            $statement->execute(['search' => '%' . strtolower($search) . '%']);
        } else {
            $statement = $this->pdo->query(
                'SELECT id, name, email, password_hash, created_at FROM users ORDER BY id DESC'
            );
        }

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row): User => $this->hydrate($row), $rows);
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, password_hash, created_at FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $this->hydrate($row);
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, password_hash, created_at FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1'
        );
        $statement->execute(['email' => trim($email)]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(string $name, string $email, string $passwordHash): User
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
        );
        $statement->execute([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'password_hash' => $passwordHash,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $created = $this->findById($id);

        if ($created === null) {
            throw new \RuntimeException('No se pudo recuperar el usuario recién creado.');
        }

        return $created;
    }

    public function update(int $id, string $name, string $email, ?string $passwordHash = null): ?User
    {
        if ($passwordHash !== null) {
            $statement = $this->pdo->prepare(
                'UPDATE users SET name = :name, email = :email, password_hash = :password_hash WHERE id = :id'
            );
            $statement->execute([
                'id' => $id,
                'name' => trim($name),
                'email' => strtolower(trim($email)),
                'password_hash' => $passwordHash,
            ]);
        } else {
            $statement = $this->pdo->prepare(
                'UPDATE users SET name = :name, email = :email WHERE id = :id'
            );
            $statement->execute([
                'id' => $id,
                'name' => trim($name),
                'email' => strtolower(trim($email)),
            ]);
        }

        return $this->findById($id);
    }

    public function updatePasswordByEmail(string $email, string $passwordHash): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET password_hash = :password_hash WHERE LOWER(email) = LOWER(:email)'
        );
        $statement->execute([
            'email' => strtolower(trim($email)),
            'password_hash' => $passwordHash,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): User
    {
        return new User(
            (int) $row['id'],
            (string) ($row['name'] ?? 'Usuario'),
            (string) $row['email'],
            (string) $row['password_hash'],
            (string) $row['created_at']
        );
    }
}
