<?php

namespace App\Challenge02_Checkout\Refactored\Repositories;

class OrderRepository {
    private PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(string $email, float $totalAmount): int {
        $stmt = $this->pdo->prepare("INSERT INTO orders (customer_email, total, status) VALUES (?, ?, 'PAID')");
        $stmt->execute([$email, $totalAmount]);
        $orderId = $this->pdo->lastInsertId();
        return (int) $orderId;
    }
}