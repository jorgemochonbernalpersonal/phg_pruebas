<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /** @return array<int, array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, created_at FROM users ORDER BY id ASC');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return \is_array($rows) ? $rows : [];
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    /** @return array<string, mixed> */
    public function create(string $name, string $email): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (name, email) VALUES (:name, :email)');
        $stmt->execute(['name' => $name, 'email' => $email]);
        $id = (int) $this->pdo->lastInsertId();
        $user = $this->findById($id);

        return $user ?? ['id' => $id, 'name' => $name, 'email' => $email, 'created_at' => null];
    }

    /** @return array<string, mixed>|null */
    public function update(int $id, string $name, string $email): ?array
    {
        if ($this->findById($id) === null) {
            return null;
        }

        $stmt = $this->pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
        $stmt->execute(['name' => $name, 'email' => $email, 'id' => $id]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
