<?php

namespace App\Challenge02_Checkout\Refactored\Domain;

class OrderCalculator {
    public function calculateTotal(array $cartItems): float {
        if (empty($cartItems)) {
            throw new \InvalidArgumentException("O carrinho não pode estar vazio.");
        }
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            /** @var CartItemVO $item */
            $totalAmount += ($item->price * $item->qty);
        }

        // Aplicação de desconto encapsulada
        return $totalAmount > 1000 ? $totalAmount * 0.90 : $totalAmount;
    }
}