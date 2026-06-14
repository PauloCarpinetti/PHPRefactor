<?php

namespace App\Challenge02_Checkout\Refactored\Services;

use App\Challenge02_Checkout\Refactored\Domain\CartItemVO;
use RuntimeException;

class InventoryService {
    public function checkAvailability(array $cartItems): void {
        foreach ($cartItems as $item) {
            /** @var CartItemVO $item */
            // Idealmente usaria um client HTTP injetado, mas a abstração já ajuda
            $stock = file_get_contents("http://api.nossoestoque.com/check/" . $item->id);
            if ((int)$stock < $item->qty) {
                throw new RuntimeException("Produto {$item->name} sem estoque.");
            }
        }
    }
}