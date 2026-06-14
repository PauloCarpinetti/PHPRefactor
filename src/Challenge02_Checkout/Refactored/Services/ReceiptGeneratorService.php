<?php

namespace App\Challenge02_Checkout\Refactored\Services;

use RuntimeException;

class ReceiptGeneratorService {
    public function __construct(
        private readonly string $storagePath = '/var/www/receipts/'
    ) {}

    public function generate(int $orderId, float $totalAmount): string {
        return "Receipt for order {$orderId} with total amount {$totalAmount}";
    }

    public function save(int $orderId, string $content): void {
        $path = "{$this->storagePath}order_{$orderId}.txt";
        if (@file_put_contents($path, $content) === false) {
            throw new RuntimeException("Não foi possível salvar o recibo do pedido #{$orderId}.");
        }
    }
}
