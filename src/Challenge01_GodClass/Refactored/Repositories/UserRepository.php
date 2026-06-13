<?php

namespace App\Challenge01_GodClass\Refactored\Repositories;

class UserRepository {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetch();
    }

    public function save(string $email, string $hashedPassword): int {
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, created_at) VALUES (:email, :password, NOW())");
        $stmt->execute(['email' => $email, 'password' => $hashedPassword]);
        return (int) $this->pdo->lastInsertId();
    }
    
}